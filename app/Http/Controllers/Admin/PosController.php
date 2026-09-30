<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Enums\Source;
use App\Models\Order;
use App\Models\DefaultAccess;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PosOrderRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\PosOrderEditResource;


class PosController extends AdminController
{
    private OrderService $orderService;

    public function __construct(OrderService $order)
    {
        parent::__construct();
        $this->orderService = $order;
        $this->middleware(['permission:pos'])->only('store', 'lastOrder', 'edit', 'update');
    }

    public function store(PosOrderRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->posOrderStore($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * The order in cart-ready form, so the POS screen can load it for editing.
     */
    public function edit(Order $order): \Illuminate\Http\Response | PosOrderEditResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PosOrderEditResource($this->orderService->show($order, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(Order $order, PosOrderRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->posOrderUpdate($order, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * The most recent POS order for the operator's branch, so the POS screen can
     * reprint an invoice after the receipt modal has been closed.
     */
    public function lastOrder(): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $branchId = $this->posBranchId();

            $order = Order::where('source', Source::POS)
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->orderByDesc('id')
                ->first();

            if (blank($order)) {
                return response(['status' => false, 'message' => __('No order found to print.')], 404);
            }

            return new OrderDetailsResource($this->orderService->show($order, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Branch the current operator is selling from. Users tied to a branch use it
     * directly; branchless users (admins) fall back to the branch they picked in
     * the POS screen, which is stored as their default access.
     */
    private function posBranchId(): ?int
    {
        $branchId = (int) (Auth::user()?->branch_id ?? 0);

        if ($branchId < 1) {
            $branchId = (int) DefaultAccess::where(
                ['user_id' => Auth::id(), 'name' => 'branch_id']
            )->value('default_id');
        }

        return $branchId > 0 ? $branchId : null;
    }

    public function nextToken(): \Illuminate\Http\Response | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $latestOrder = \App\Models\Order::whereDate('created_at', date('Y-m-d'))
                ->whereNotNull('token')
                ->where('token', '!=', '')
                ->orderBy('id', 'desc')
                ->first();

            $token = 1;
            if ($latestOrder) {
                $token = (int) $latestOrder->token + 1;
            }
            $paddedToken = str_pad($token, 4, '0', STR_PAD_LEFT);
            return response(['status' => true, 'data' => $paddedToken], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}