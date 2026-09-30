<?php

namespace App\Services;

use Exception;
use App\Models\CustomerLoyaltyPoint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendLoyaltyPointService
{
    /**
     * Get all loyalty points for a customer with pagination
     *
     * @param User $customer
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function getCustomerLoyaltyPoints(User $customer, Request $request): array
    {
        try {
            $query = CustomerLoyaltyPoint::where('customer_id', $customer->id)
                ->with(['order' => function($query) {
                    $query->select('id', 'order_serial_no');
                }])
                ->orderBy('created_at', 'desc');

            // Apply pagination if requested
            $perPage = $request->get('per_page', 10);
            $loyaltyPoints = $query->paginate($perPage);

            // Add status_text to each item in the paginated collection
            $loyaltyPoints->getCollection()->transform(function ($item) {
                $isExpired = $item->expired_at && $item->expired_at <= now();
                $item->status_text = ($item->status == 5 && !$isExpired) ? 'Active' : 'Expired';
                return $item;
            });

            // Calculate summary
            $summary = $this->getCustomerLoyaltyPointsSummary($customer);

            $response = [
                'data' => $loyaltyPoints,
                'total_points' => $summary['total_points'],
                'active_points' => $summary['active_points'],
                'expired_points' => $summary['expired_points'],
                'meta' => [
                    'current_page' => $loyaltyPoints->currentPage(),
                    'per_page' => $loyaltyPoints->perPage(),
                    'total' => $loyaltyPoints->total(),
                    'last_page' => $loyaltyPoints->lastPage(),
                    'from' => $loyaltyPoints->firstItem(),
                    'to' => $loyaltyPoints->lastItem(),
                ]
            ];

            return $response;
        } catch (Exception $exception) {
            Log::error('Error fetching customer loyalty points: ' . $exception->getMessage());
            throw new Exception('Failed to fetch loyalty points', 500);
        }
    }

    /**
     * Get specific loyalty point details for a customer
     *
     * @param User $customer
     * @param int $loyaltyPointId
     * @return CustomerLoyaltyPoint
     * @throws Exception
     */
    public function getCustomerLoyaltyPoint(User $customer, int $loyaltyPointId): CustomerLoyaltyPoint
    {
        try {
            $loyaltyPoint = CustomerLoyaltyPoint::where('customer_id', $customer->id)
                ->where('id', $loyaltyPointId)
                ->with(['order', 'customer'])
                ->first();

            if (!$loyaltyPoint) {
                throw new Exception('Loyalty point not found', 404);
            }

            return $loyaltyPoint;
        } catch (Exception $exception) {
            Log::error('Error fetching customer loyalty point: ' . $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Get loyalty points summary for a customer
     *
     * @param User $customer
     * @return array
     * @throws Exception
     */
    public function getCustomerLoyaltyPointsSummary(User $customer): array
    {
        try {
            $totalPoints = CustomerLoyaltyPoint::where('customer_id', $customer->id)
                ->where('status', 5) // Active status
                ->sum('points');

            $activePoints = CustomerLoyaltyPoint::where('customer_id', $customer->id)
                ->where('status', 5) // Active status
                ->where(function($query) {
                    $query->whereNull('expired_at')
                          ->orWhere('expired_at', '>', now());
                })
                ->sum('points');

            $expiredPoints = CustomerLoyaltyPoint::where('customer_id', $customer->id)
                ->where('status', 5) // Active status
                ->where('expired_at', '<=', now())
                ->sum('points');

            return [
                'total_points' => $totalPoints,
                'active_points' => $activePoints,
                'expired_points' => $expiredPoints,
            ];
        } catch (Exception $exception) {
            Log::error('Error fetching customer loyalty points summary: ' . $exception->getMessage());
            throw new Exception('Failed to fetch loyalty points summary', 500);
        }
    }

    /**
     * Get loyalty points summary with recent points for a customer
     *
     * @param User $customer
     * @param int $recentLimit
     * @return array
     * @throws Exception
     */
    public function getCustomerLoyaltyPointsSummaryWithRecent(User $customer, int $recentLimit = 10): array
    {
        try {
            $summary = $this->getCustomerLoyaltyPointsSummary($customer);

            // Get recent loyalty points history
            $recentPoints = CustomerLoyaltyPoint::where('customer_id', $customer->id)
                ->with(['order' => function($query) {
                    $query->select('id', 'order_serial_no');
                }])
                ->orderBy('created_at', 'desc')
                ->limit($recentLimit)
                ->get();




            // Add status_text to recent points
            $recentPoints->transform(function ($item) {
                $isExpired = $item->expired_at && $item->expired_at <= now();
                $item->status_text = ($item->status == 5 && !$isExpired) ? 'Active' : 'Expired';
                return $item;
            });
            $customer_type = $customer->isGuest() ? 'guest' : 'authorized';
            return array_merge($summary, [
                'recent_points' => $recentPoints,
                'customer_type' => $customer_type
            ]);
        } catch (Exception $exception) {
            Log::error('Error fetching customer loyalty points summary with recent: ' . $exception->getMessage());
            throw new Exception('Failed to fetch loyalty points summary with recent points', 500);
        }
    }
}