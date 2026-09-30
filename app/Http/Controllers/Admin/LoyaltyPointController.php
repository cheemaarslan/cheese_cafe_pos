<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LoyaltyPointRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\LoyaltyPointResource;
use App\Models\LoyaltyPoint;
use App\Services\LoyaltyPointService;
use Exception;


class LoyaltyPointController extends AdminController
{

    public LoyaltyPointService $loyaltyPointService;

    public function __construct(LoyaltyPointService $loyaltyPointService)
    {
        parent::__construct();
        $this->loyaltyPointService = $loyaltyPointService;
        $this->middleware(['permission:settings'])->only('show', 'store', 'update', 'destroy');
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response([
                'data' => $this->loyaltyPointService->list($request)
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(LoyaltyPointRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|LoyaltyPointResource
    {
        try {
            return new LoyaltyPointResource($this->loyaltyPointService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(LoyaltyPointRequest $request, LoyaltyPoint $loyaltyPoint): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|LoyaltyPointResource
    {
        try {
            return new LoyaltyPointResource($this->loyaltyPointService->update($request, $loyaltyPoint));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(LoyaltyPoint $loyaltyPoint): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->loyaltyPointService->destroy($loyaltyPoint);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(LoyaltyPoint $loyaltyPoint): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|LoyaltyPointResource
    {
        try {
            return response([
                'data' => $this->loyaltyPointService->show($loyaltyPoint)
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
