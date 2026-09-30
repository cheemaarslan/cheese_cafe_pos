<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\FrontendLoyaltyPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyPointController extends Controller
{
    protected $loyaltyPointService;

    public function __construct(FrontendLoyaltyPointService $loyaltyPointService)
    {
        $this->loyaltyPointService = $loyaltyPointService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $customer = Auth::guard('sanctum')->user();
            $result = $this->loyaltyPointService->getCustomerLoyaltyPoints($customer, $request);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch loyalty points',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($loyaltyPointId)
    {
        try {
            $customer = Auth::guard('sanctum')->user();
            $loyaltyPoint = $this->loyaltyPointService->getCustomerLoyaltyPoint($customer, $loyaltyPointId);

            return response()->json([
                'data' => $loyaltyPoint
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Get loyalty points summary for the customer
     */
    public function summary(Request $request)
    {
        try {
            $customer = Auth::guard('sanctum')->user();
            $result = $this->loyaltyPointService->getCustomerLoyaltyPointsSummaryWithRecent($customer);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch loyalty points summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}