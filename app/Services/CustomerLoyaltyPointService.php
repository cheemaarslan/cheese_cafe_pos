<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\CustomerLoyaltyPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class CustomerLoyaltyPointService
{
    /**
     * @throws Exception
     */
    public $customerLoyaltyPoint;
    public $customerLoyaltyPointFilter = ['order_subtotal', 'percentage', 'points', 'status'];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, User $customer)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return CustomerLoyaltyPoint::where('customer_id', $customer->id)->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->customerLoyaltyPointFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
    public function store($request, User $customer): CustomerLoyaltyPoint
    {
        try {
            DB::transaction(function () use ($request, $customer) {
                $this->customerLoyaltyPoint = CustomerLoyaltyPoint::create($request->validated() + ['customer_id' => $customer->id]);
            });
            return $this->customerLoyaltyPoint;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update($request, User $customer, CustomerLoyaltyPoint $customerLoyaltyPoint)
    {
        try {
            if ($customer->id == $customerLoyaltyPoint->customer_id) {
                return tap($customerLoyaltyPoint)->update($request->validated());
            } else {
                throw new Exception(trans('all.user_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(User $customer, CustomerLoyaltyPoint $customerLoyaltyPoint): void
    {
        try {
            if ($customer->id == $customerLoyaltyPoint->customer_id) {
                $customerLoyaltyPoint->delete();
            } else {
                throw new Exception(trans('all.user_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(User $customer, CustomerLoyaltyPoint $customerLoyaltyPoint): CustomerLoyaltyPoint
    {
        try {
            if ($customer->id == $customerLoyaltyPoint->customer_id) {
                return $customerLoyaltyPoint;
            } else {
                throw new Exception(trans('all.user_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}