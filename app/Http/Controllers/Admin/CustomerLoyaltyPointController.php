<?php

namespace App\Http\Controllers\Admin;

use App\Services\CustomerLoyaltyPointService;
use Exception;
use App\Models\User;
use App\Models\CustomerLoyaltyPoint;
use App\Http\Requests\CustomerLoyaltyPointRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CustomerLoyaltyPointResource;

class CustomerLoyaltyPointController extends AdminController
{

    private CustomerLoyaltyPointService $customerLoyaltyPointService;

    public function __construct(CustomerLoyaltyPointService $customerLoyaltyPointService)
    {
        parent::__construct();
        $this->customerLoyaltyPointService = $customerLoyaltyPointService;
        $this->middleware(['permission:customers_show'])->only('index', 'store', 'update', 'destroy', 'show');
    }

    public function index(PaginateRequest $request, User $customer): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CustomerLoyaltyPointResource::collection($this->customerLoyaltyPointService->list($request, $customer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CustomerLoyaltyPointRequest $request, User $customer): \Illuminate\Http\Response | CustomerLoyaltyPointResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CustomerLoyaltyPointResource($this->customerLoyaltyPointService->store($request, $customer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(CustomerLoyaltyPointRequest $request, User $customer, CustomerLoyaltyPoint $customerLoyaltyPoint): \Illuminate\Http\Response | CustomerLoyaltyPointResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CustomerLoyaltyPointResource($this->customerLoyaltyPointService->update($request, $customer, $customerLoyaltyPoint));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $customer, CustomerLoyaltyPoint $customerLoyaltyPoint): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->customerLoyaltyPointService->destroy($customer, $customerLoyaltyPoint);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $customer, CustomerLoyaltyPoint $customerLoyaltyPoint): \Illuminate\Http\Response | CustomerLoyaltyPointResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CustomerLoyaltyPointResource($this->customerLoyaltyPointService->show($customer, $customerLoyaltyPoint));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}