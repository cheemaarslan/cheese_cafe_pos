<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\Source;
use App\Services\AdminNotificationService;
use App\Enums\Status;
use App\Enums\TaxType;
use App\Events\SendOrderDeliveryBoyMail;
use App\Events\SendOrderDeliveryBoyPush;
use App\Events\SendOrderDeliveryBoySms;
use App\Events\SendOrderGotMail;
use App\Events\SendOrderGotPush;
use App\Events\SendOrderGotSms;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use App\Events\SendOrderSms;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PaymentStatusRequest;
use App\Http\Requests\PosOrderRequest;
use App\Http\Requests\TableOrderRequest;
use App\Http\Requests\TableOrderTokenRequest;
use App\Libraries\AppLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Address;
use App\Models\FrontendOrder;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderCoupon;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\PaymentGateway;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;

class OrderService
{
    public object $order;

    protected array $orderFilter = [
        'order_serial_no',
        'user_id',
        'branch_id',
        'total',
        'order_type',
        'order_datetime',
        'payment_method',
        'payment_status',
        'status',
        'delivery_boy_id',
        'source',
    ];

    private AdminNotificationService $adminNotificationService;

    public function __construct(AdminNotificationService $adminNotificationService)
    {
        $this->adminNotificationService = $adminNotificationService;
    }

    protected array $exceptFilter = [
        'excepts',
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_by') ?? 'desc';

            return Order::with('transaction', 'orderItems', 'branch', 'user', 'diningTable')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('order_datetime', '>=', $first_date)->whereDate(
                        'order_datetime',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->orderFilter)) {
                        if ($key === 'status') {
                            $query->where($key, (int) $request);
                        } elseif ($key === 'payment_method') {
                            if ((int) $request > 0) {
                                if ((int) $request === 1) {
                                    $query->where('payment_method', 1)->where('pos_payment_method', null)->whereDoesntHave('transaction');
                                } else {
                                    $paymentGateway = PaymentGateway::findOrFail((int) $request);
                                    $query->whereHas('transaction', function ($q) use ($paymentGateway) {
                                        $q->where('payment_method', $paymentGateway->slug);
                                    });
                                }
                            } else {
                                $query->where('pos_payment_method', abs((int) $request));
                            }
                        } elseif ($key === 'source') {
                            $query->where($key, $request);
                        } else {
                            $query->where($key, 'like', '%'.$request.'%');
                        }
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('order_type', '!=', $explode);
                            }
                        }
                    }
                }

                // Add condition for "exceptSource"
                if (isset($requests['exceptSource'])) {
                    $query->where('source', '!=', $requests['exceptSource']);
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function userOrder(PaginateRequest $request, User $user)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_by') ?? 'desc';

            return Order::with('transaction', 'orderItems', 'branch', 'user')->where('order_type', '!=', OrderType::POS)->where(function ($query) use ($requests, $user) {
                $query->where('user_id', $user->id);
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->orderFilter)) {
                        $query->where($key, 'like', '%'.$request.'%');
                    }
                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('status', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveredOrder(PaginateRequest $request, User $user)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_by') ?? 'desc';

            return Order::where('delivery_boy_id', $user->id)->where('order_type', '!=', OrderType::POS)->where(
                function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->orderFilter)) {
                            $query->where($key, 'like', '%'.$request.'%');
                        }
                        if (in_array($key, $this->exceptFilter)) {
                            $explodes = explode('|', $request);
                            if (is_array($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('status', '!=', $explode);
                                }
                            }
                        }
                    }
                }
            )->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyOrder(PaginateRequest $request)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_by') ?? 'desc';

            return Order::where('order_type', '!=', OrderType::POS)->where('delivery_boy_id', Auth::user()->id)->where(
                function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->orderFilter)) {
                            $query->where($key, 'like', '%'.$request.'%');
                        }
                        if (in_array($key, $this->exceptFilter)) {
                            $explodes = explode('|', $request);
                            if (is_array($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('status', '!=', $explode);
                                }
                            }
                        }
                    }
                }
            )->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function myOrderStore(OrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->order = Order::create(
                    $request->validated() + [
                        'user_id' => Auth::user()->id,
                        'status' => OrderStatus::PENDING,
                        'order_datetime' => date('Y-m-d H:i:s'),
                        'preparation_time' => Settings::group('order_setup')->get('order_setup_food_preparation_time'),
                    ]
                );

                $i = 0;
                $totalTax = 0;
                $itemsArray = [];
                $requestItems = json_decode($request->items);
                $items = Item::get()->pluck('tax_id', 'id');
                $taxes = AppLibrary::pluck(Tax::get(), 'obj', 'id');

                if (! blank($requestItems)) {
                    foreach ($requestItems as $item) {
                        // Determine itemable_type and itemable_id
                        $itemableType = $item->itemable_type ?? 'App\\Models\\Item';
                        $itemableId = $item->itemable_id ?? $item->item_id;

                        $taxId = isset($items[$item->item_id]) ? $items[$item->item_id] : 0;
                        $taxName = isset($taxes[$taxId]) ? $taxes[$taxId]->name : null;
                        $taxRate = isset($taxes[$taxId]) ? $taxes[$taxId]->tax_rate : 0;
                        $taxType = isset($taxes[$taxId]) ? $taxes[$taxId]->type : TaxType::FIXED;
                        $taxPrice = $taxType === TaxType::FIXED ? $taxRate : ($item->total_price * $taxRate) / 100;
                        $itemsArray[$i] = [
                            'order_id' => $this->order->id,
                            'branch_id' => $item->branch_id,
                            'item_id' => $item->item_id ?? null,
                            'itemable_type' => $itemableType,
                            'itemable_id' => $itemableId,
                            'quantity' => $item->quantity,
                            'discount' => (float) $item->discount,
                            'tax_name' => $taxName,
                            'tax_rate' => $taxRate,
                            'tax_type' => $taxType,
                            'tax_amount' => $taxPrice,
                            'price' => $item->item_price,
                            'item_variations' => json_encode($item->item_variations),
                            'item_extras' => json_encode($item->item_extras),
                            'instruction' => $item->instruction,
                            'item_variation_total' => $item->item_variation_total,
                            'item_extra_total' => $item->item_extra_total,
                            'total_price' => $item->total_price,
                        ];
                        $totalTax = $totalTax + $taxPrice;
                        $i++;
                    }
                }

                if (! blank($itemsArray)) {
                    OrderItem::insert($itemsArray);
                }

                $nextSeq = ((int) \App\Models\Order::withoutGlobalScopes()
                    ->selectRaw('MAX(CAST(order_serial_no AS UNSIGNED)) as seq')
                    ->value('seq')) + 1;
                $this->order->order_serial_no = $nextSeq;
                $this->order->total_tax = $totalTax;
                $this->order->save();

                if ($request->address_id) {
                    $address = Address::find($request->address_id);
                    if ($address) {
                        OrderAddress::create([
                            'order_id' => $this->order->id,
                            'user_id' => Auth::user()->id,
                            'label' => $address->label,
                            'address' => $address->address,
                            'apartment' => $address->apartment,
                            'latitude' => $address->latitude,
                            'longitude' => $address->longitude,
                        ]);
                    }
                }

                if ($request->coupon_id > 0) {
                    OrderCoupon::create([
                        'order_id' => $this->order->id,
                        'coupon_id' => $request->coupon_id,
                        'user_id' => Auth::user()->id,
                        'discount' => $request->discount,
                    ]);
                }

                SendOrderMail::dispatch(['order_id' => $this->order->id, 'status' => $request->status]);
                SendOrderSms::dispatch(['order_id' => $this->order->id, 'status' => $request->status]);
                SendOrderPush::dispatch(['order_id' => $this->order->id, 'status' => $request->status]);
            });

            return $this->order;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function posOrderStore(PosOrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->order = Order::create(
                    $request->validated() + [
                        'user_id' => $request->customer_id,
                        'status' => OrderStatus::CONFIRMED,
                        'token' => $request->token,
                        'payment_status' => PaymentStatus::PAID,
                        'order_datetime' => date('Y-m-d H:i:s'),
                        'preparation_time' => Settings::group('order_setup')->get('order_setup_food_preparation_time'),
                        'is_viewed' => true,
                    ]
                );

                $totalTax = $this->insertPosOrderItems($this->order, $request);

                $nextSeq = ((int) \App\Models\Order::withoutGlobalScopes()
                    ->selectRaw('MAX(CAST(order_serial_no AS UNSIGNED)) as seq')
                    ->value('seq')) + 1;
                $this->order->order_serial_no = $nextSeq;
                $this->order->total_tax = $totalTax;
                $currentTime = Carbon::now();
                $endTime = $currentTime->copy()->addMinutes(Settings::group('order_setup')->get('order_setup_schedule_order_slot_duration'));
                $start = $currentTime->format('H:i');
                $end = $endTime->format('H:i');
                $this->order->delivery_time = "$start - $end";
                $this->order->save();

                $this->storePosOrderAddress($this->order, $request);
                $this->inventory()->orderPlaced($this->order);
            });

            return $this->order;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Rewrites an existing POS order in place (items, customer, order type, amounts,
     * payment) so a corrected invoice can be printed under the same order number and
     * token instead of placing a second order.
     *
     * @throws Exception
     */
    public function posOrderUpdate(Order $order, PosOrderRequest $request): object
    {
        try {
            if ((int) $order->source !== Source::POS) {
                throw new Exception(trans('all.message.only_pos_order_editable'), 422);
            }
            if (in_array((int) $order->status, [OrderStatus::CANCELED, OrderStatus::REJECTED], true)) {
                throw new Exception(trans('all.message.canceled_order_not_editable'), 422);
            }

            DB::transaction(function () use ($order, $request) {
                $order->fill([
                    'user_id' => $request->customer_id,
                    'subtotal' => $request->subtotal,
                    'discount' => $request->discount ?? 0,
                    'delivery_charge' => $request->delivery_charge ?? 0,
                    'total' => $request->total,
                    'order_type' => $request->order_type,
                    'dining_table_id' => $request->dining_table_id,
                    'instruction' => $request->instruction,
                    'pos_payment_method' => $request->pos_payment_method,
                    'pos_payment_note' => $request->pos_payment_note,
                    'pos_received_amount' => $request->pos_received_amount,
                    // The kitchen has to look at the changed items again
                    'chef_viewed_at' => null,
                ]);

                $order->orderItems()->delete();
                $order->total_tax = $this->insertPosOrderItems($order, $request);
                $order->save();

                $order->address()?->delete();
                $this->storePosOrderAddress($order, $request);
                $this->inventory()->orderUpdated($order);
            });

            $this->order = $order->fresh();

            return $this->order;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Saves the POS cart items sent in the request against the order and returns
     * the total tax of those items.
     */
    private function insertPosOrderItems(Order $order, PosOrderRequest $request): float
    {
        $i = 0;
        $totalTax = 0;
        $itemsArray = [];
        $requestItems = json_decode($request->items);
        $items = Item::get()->pluck('tax_id', 'id');
        $taxes = AppLibrary::pluck(Tax::get(), 'obj', 'id');

        if (! blank($requestItems)) {
            foreach ($requestItems as $item) {
                // Custom (extra) POS items aren't linked to any item/offer, only a name
                $isCustom = ! empty($item->is_custom);

                // Determine itemable_type and itemable_id
                $itemableType = $isCustom ? null : ($item->itemable_type ?? 'App\\Models\\Item');
                $itemableId = $isCustom ? null : ($item->itemable_id ?? $item->item_id);

                $taxId = ! $isCustom && isset($items[$item->item_id]) ? $items[$item->item_id] : 0;
                $taxName = isset($taxes[$taxId]) ? $taxes[$taxId]->name : null;
                $taxRate = isset($taxes[$taxId]) ? $taxes[$taxId]->tax_rate : 0;
                $taxType = isset($taxes[$taxId]) ? $taxes[$taxId]->type : TaxType::FIXED;
                $taxPrice = $taxType === TaxType::FIXED ? $taxRate : ($item->total_price * $taxRate) / 100;
                $itemsArray[$i] = [
                    'order_id' => $order->id,
                    'branch_id' => $item->branch_id,
                    'item_id' => $item->item_id ?? null,
                    'itemable_type' => $itemableType,
                    'itemable_id' => $itemableId,
                    'item_name' => $isCustom ? ($item->item_name ?? null) : null,
                    'quantity' => $item->quantity,
                    'discount' => (float) $item->discount,
                    'tax_name' => $taxName,
                    'tax_rate' => $taxRate,
                    'tax_type' => $taxType,
                    'tax_amount' => $taxPrice,
                    'price' => $item->item_price,
                    'item_variations' => json_encode($item->item_variations),
                    'item_extras' => json_encode($item->item_extras),
                    'instruction' => $item->instruction,
                    'item_variation_total' => $item->item_variation_total,
                    'item_extra_total' => $item->item_extra_total,
                    'total_price' => $item->total_price,
                ];
                $totalTax = $totalTax + $taxPrice;
                $i++;
            }
        }

        if (! blank($itemsArray)) {
            OrderItem::insert($itemsArray);
        }

        return $totalTax;
    }

    /**
     * Copies the chosen customer address onto the order, so later edits to the
     * customer's saved address don't change old invoices.
     */
    private function storePosOrderAddress(Order $order, PosOrderRequest $request): void
    {
        if ($request->address_id) {
            $address = Address::find($request->address_id);
            if ($address) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'user_id' => $request->customer_id,
                    'label' => $address->label,
                    'address' => $address->address,
                    'apartment' => $address->apartment,
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                ]);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function tableOrderStore(TableOrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->order = FrontendOrder::create(
                    $request->validated() + [
                        'user_id' => $request->customer_id,
                        'status' => OrderStatus::PENDING,
                        'order_datetime' => date('Y-m-d H:i:s'),
                        'preparation_time' => Settings::group('order_setup')->get('order_setup_food_preparation_time'),
                    ]
                );

                $i = 0;
                $totalTax = 0;
                $itemsArray = [];
                $requestItems = json_decode($request->items);
                $items = Item::get()->pluck('tax_id', 'id');
                $taxes = AppLibrary::pluck(Tax::get(), 'obj', 'id');

                if (! blank($requestItems)) {
                    foreach ($requestItems as $item) {
                        // Determine itemable_type and itemable_id
                        $itemableType = $item->itemable_type ?? 'App\\Models\\Item';
                        $itemableId = $item->itemable_id ?? $item->item_id;

                        $taxId = isset($items[$item->item_id]) ? $items[$item->item_id] : 0;
                        $taxName = isset($taxes[$taxId]) ? $taxes[$taxId]->name : null;
                        $taxRate = isset($taxes[$taxId]) ? $taxes[$taxId]->tax_rate : 0;
                        $taxType = isset($taxes[$taxId]) ? $taxes[$taxId]->type : TaxType::FIXED;
                        $taxPrice = $taxType === TaxType::FIXED ? $taxRate : ($item->total_price * $taxRate) / 100;
                        $itemsArray[$i] = [
                            'order_id' => $this->order->id,
                            'branch_id' => $item->branch_id,
                            'item_id' => $item->item_id ?? null,
                            'itemable_type' => $itemableType,
                            'itemable_id' => $itemableId,
                            'quantity' => $item->quantity,
                            'discount' => (float) $item->discount,
                            'tax_name' => $taxName,
                            'tax_rate' => $taxRate,
                            'tax_type' => $taxType,
                            'tax_amount' => $taxPrice,
                            'price' => $item->item_price,
                            'item_variations' => json_encode($item->item_variations),
                            'item_extras' => json_encode($item->item_extras),
                            'instruction' => $item->instruction,
                            'item_variation_total' => $item->item_variation_total,
                            'item_extra_total' => $item->item_extra_total,
                            'total_price' => $item->total_price,
                        ];
                        $totalTax = $totalTax + $taxPrice;
                        $i++;
                    }
                }

                if (! blank($itemsArray)) {
                    OrderItem::insert($itemsArray);
                }

                $nextSeq = ((int) \App\Models\Order::withoutGlobalScopes()
                    ->selectRaw('MAX(CAST(order_serial_no AS UNSIGNED)) as seq')
                    ->value('seq')) + 1;
                $this->order->order_serial_no = $nextSeq;
                $this->order->total_tax = $totalTax;
                $currentTime = Carbon::now();
                $endTime = $currentTime->copy()->addMinutes(Settings::group('order_setup')->get('order_setup_schedule_order_slot_duration'));
                $start = $currentTime->format('H:i');
                $end = $endTime->format('H:i');
                $this->order->delivery_time = "$start - $end";
                $this->order->save();

                SendOrderGotMail::dispatch(['order_id' => $this->order->id]);
                SendOrderGotSms::dispatch(['order_id' => $this->order->id]);
                SendOrderGotPush::dispatch(['order_id' => $this->order->id]);
            });

            // send notification to admin

            $this->adminNotificationService->createForAllAdmins('new_order', [
                'message' => 'New order table received: #'.$this->order->order_serial_no,
                'url' => '/admin/table-orders/show/'.$this->order->id,
                'order_id' => $this->order->id,
                'order_serial_no' => $this->order->order_serial_no,
                'order_type' => 'table',
            ]);

            return $this->order;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Order $order, $auth = false): Order|array
    {
        try {
            if ($auth) {
                if ($order->user_id == Auth::user()->id) {
                    return $order;
                } else {
                    return [];
                }
            } else {
                return $order;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function orderDetails(User $user, Order $order): Order|array
    {
        try {
            if ($order->user_id == $user->id) {
                return $order->load('transaction', 'orderItems', 'branch', 'user');
            } else {
                return [];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyOrderDetails(Order $order): Order|array
    {
        try {
            if ($order->delivery_boy_id == Auth::user()->id) {
                return $order;
            } else {
                return [];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyDeliveredOrderDetails(User $user, Order $order): Order|array
    {
        try {
            if ($order->delivery_boy_id == $user->id) {
                return $order;
            } else {
                return [];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyOrderCount(): array
    {
        try {
            $order = new Order;
            $orderCountArray = [];
            $orderCountArray['total_delivered'] = $order->where(
                ['delivery_boy_id' => Auth::user()->id, 'status' => OrderStatus::DELIVERED]
            )->count();
            $orderCountArray['total_returned'] = $order->where(
                ['delivery_boy_id' => Auth::user()->id, 'status' => OrderStatus::RETURNED]
            )->count();

            return $orderCountArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyOrderChangeStatus(Order $order, OrderStatusRequest $request): Order
    {
        try {
            $transaction = Transaction::where('order_id', $order->id)->first();

            if (! $transaction && $order->payment_status == PaymentStatus::UNPAID) {
                $order->payment_status = PaymentStatus::PAID;
            }
            SendOrderMail::dispatch(['order_id' => $order->id, 'status' => OrderStatus::DELIVERED]);
            SendOrderSms::dispatch(['order_id' => $order->id, 'status' => OrderStatus::DELIVERED]);
            SendOrderPush::dispatch(['order_id' => $order->id, 'status' => OrderStatus::DELIVERED]);
            $order->status = OrderStatus::DELIVERED;
            $order->save();

            return $order;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(Order $order, $auth, OrderStatusRequest $request): Order|array
    {
        try {
            $fromStatus = $order->status;

            if ($auth) {
                if ($order->user_id == Auth::user()->id) {
                    if ($request->reason) {
                        $order->reason = $request->reason;
                    }

                    if ($request->status == OrderStatus::REJECTED || $request->status == OrderStatus::CANCELED) {
                        if ($order->transaction) {
                            app(PaymentService::class)->cashBack(
                                $order,
                                'credit',
                                rand(111111111111111, 99999999999999)
                            );
                        }
                    }

                    SendOrderMail::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                    SendOrderSms::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                    SendOrderPush::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                    $order->status = $request->status;
                    $order->save();
                    $this->recordStatusHistory($order, $fromStatus, 'customer', $request->reason);
                    $this->inventory()->statusChanged($order);

                    if ($order->user) {
                        $order->user->notify(new OrderStatusUpdated($order));
                    }

                    return $order;
                }
            } else {
                if ($request->status == OrderStatus::REJECTED || $request->status == OrderStatus::CANCELED) {
                    $request->validate([
                        'reason' => 'required|max:700',
                    ]);

                    if ($request->reason) {
                        $order->reason = $request->reason;
                    }

                    if ($order->transaction) {
                        app(PaymentService::class)->cashBack(
                            $order,
                            'credit',
                            rand(111111111111111, 99999999999999)
                        );
                    }
                }

                if ($request->status == OrderStatus::ACCEPT) {
                    $order->loyaltyPoint()->update(['status' => Status::ACTIVE]);
                }

                SendOrderMail::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                SendOrderSms::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                SendOrderPush::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                $order->status = $request->status;
                $order->save();
                $this->recordStatusHistory($order, $fromStatus, 'admin', $request->reason);
                $this->inventory()->statusChanged($order);
            }

            return $order;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Append one row to the order's status-change audit trail. Called right after
     * $order->status has already been changed and saved, so $order->status is the "to" value.
     * The changer's name/role is snapshotted at write time so the history stays readable even
     * if that account is later renamed or deleted.
     */
    private function recordStatusHistory(Order $order, $fromStatus, string $source, ?string $reason = null): void
    {
        $changedById   = null;
        $changedByName = null;

        $user = Auth::user();
        if ($user) {
            $changedById = $user->id;
            $role        = $user->relationLoaded('roles') ? $user->roles->first() : $user->roles()->first();
            $changedByName = $role ? "{$user->name} ({$role->name})" : $user->name;
        }

        OrderStatusHistory::create([
            'order_id'          => $order->id,
            'from_status'       => $fromStatus,
            'to_status'         => $order->status,
            'reason'            => $reason,
            'changed_by_id'     => $changedById,
            'changed_by_name'   => $changedByName,
            'changed_by_source' => $source,
            'created_at'        => now(),
        ]);
    }

    /**
     * Keeps POS orders' recipe ingredients in step with inventory; see OrderInventoryService.
     */
    private function inventory(): OrderInventoryService
    {
        return app(OrderInventoryService::class);
    }

    /**
     * @throws Exception
     */
    public function changePaymentStatus(Order $order, $auth, PaymentStatusRequest $request): Order|array
    {
        try {
            if ($auth) {
                if ($order->user_id == Auth::user()->id) {
                    $order->payment_status = $request->payment_status;
                    $order->save();

                    return $order;
                } else {
                    return [];
                }
            } else {
                $order->payment_status = $request->payment_status;
                $order->save();

                return $order;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function tokenCreate(Order $order, $auth, TableOrderTokenRequest $request): Order|array
    {
        try {
            if ($auth) {
                if ($order->user_id == Auth::user()->id) {
                    $order->token = $request->token;
                    $order->save();

                    return $order;
                } else {
                    return [];
                }
            } else {
                $order->token = $request->token;
                $order->save();

                return $order;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function selectDeliveryBoy(Order $order, $auth, Request $request): Order|array
    {
        try {
            if ($auth) {
                if ($order->user_id == Auth::user()->id) {
                    $order->delivery_boy_id = $request->delivery_boy_id;
                    $order->save();
                    SendOrderDeliveryBoyMail::dispatch(['order_id' => $order->id, 'status' => 101]);
                    SendOrderDeliveryBoySms::dispatch(['order_id' => $order->id, 'status' => 101]);
                    SendOrderDeliveryBoyPush::dispatch(['order_id' => $order->id, 'status' => 101]);

                    return $order;
                } else {
                    return [];
                }
            } else {
                $order->delivery_boy_id = $request->delivery_boy_id;
                $order->save();
                SendOrderDeliveryBoyMail::dispatch(['order_id' => $order->id, 'status' => 101]);
                SendOrderDeliveryBoySms::dispatch(['order_id' => $order->id, 'status' => 101]);
                SendOrderDeliveryBoyPush::dispatch(['order_id' => $order->id, 'status' => 101]);

                return $order;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $this->inventory()->orderDeleting($order);
                $order->address()?->delete();
                $order->coupon()?->delete();
                $order->orderItems()?->delete();
                $order->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function salesReportOverview(Request $request)
    {
        try {
            $requests = $request->all();
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_by') ?? 'desc';

            $orders = Order::with('transaction', 'orderItems')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('order_datetime', '>=', $first_date)->whereDate(
                        'order_datetime',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->orderFilter)) {
                        if ($key === 'status') {
                            $query->where($key, (int) $request);
                        } elseif ($key === 'payment_method') {
                            if ((int) $request > 0) {
                                if ((int) $request === 1) {
                                    $query->where('payment_method', 1)->where('pos_payment_method', null)->whereDoesntHave('transaction');
                                } else {
                                    $paymentGateway = PaymentGateway::findOrFail((int) $request);
                                    $query->whereHas('transaction', function ($q) use ($paymentGateway) {
                                        $q->where('payment_method', $paymentGateway->slug);
                                    });
                                }
                            } else {
                                $query->where('pos_payment_method', abs((int) $request));
                            }
                        } elseif ($key === 'source') {
                            $query->where($key, $request);
                        } else {
                            $query->where($key, 'like', '%'.$request.'%');
                        }
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('order_type', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->get();
            $salesReportArray = [];

            $salesReportArray['total_orders'] = $orders->count();
            $salesReportArray['total_earnings'] = AppLibrary::currencyAmountFormat($orders->sum('total'));
            $salesReportArray['total_discounts'] = AppLibrary::currencyAmountFormat($orders->sum('discount'));
            $salesReportArray['total_delivery_charges'] = AppLibrary::currencyAmountFormat($orders->sum('delivery_charge'));

            return $salesReportArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
