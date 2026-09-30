<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    /**
     * Get admin user notifications
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);

            $notifications = AdminNotification::where('admin_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'last_page' => $notifications->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch notifications'], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function read($id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $notification = AdminNotification::where('id', $id)
                ->where('admin_id', $user->id)
                ->first();

            if (! $notification) {
                return response()->json(['message' => 'Notification not found'], 404);
            }

            $notification->update(['read_at' => now()]);

            return response()->json(['message' => 'Notification marked as read', 'data' => $notification]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to mark notification as read'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function readAll()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            AdminNotification::where('admin_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to mark all notifications as read'], 500);
        }
    }

    /**
     * Get count of orders that have not been viewed by admin yet.
     */
    public function unviewedCount()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Only admins should receive unviewed order notifications
            if (! $user->hasRole(\App\Enums\Role::ADMIN)) {
                return response()->json(['count' => 0, 'single_order_id' => null]);
            }

            // withoutGlobalScopes bypasses BranchScope so admins see all branches
            $orders = Order::withoutGlobalScopes()
                ->where('is_viewed', false)
                ->whereNull('token')
                ->where('order_type', '!=', \App\Enums\OrderType::POS)
                ->get(['id', 'order_type']);

            $count = $orders->count();
            $singleOrderId = $count === 1 ? $orders->first()->id : null;
            $singleOrderType = $count === 1 ? $orders->first()->order_type : null;

            return response()->json(['count' => $count, 'single_order_id' => $singleOrderId, 'single_order_type' => $singleOrderType]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch unviewed count', 'error' => $e->getMessage()], 500);
        }
    }

    public function chefUnviewedCount()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            if (! $user->hasRole(\App\Enums\Role::CHEF)) {
                return response()->json(['count' => 0, 'single_order_id' => null]);
            }

            $orders = Order::withoutGlobalScopes()
                ->whereNull('chef_viewed_at')
                ->where('status', \App\Enums\OrderStatus::CONFIRMED)
                // ->where('is_viewed', true)
                ->whereDate('order_datetime', now()->toDateString())
                ->get(['id', 'order_serial_no', 'order_type', 'delivery_time', 'token']);

            $count = $orders->count();
            $singleOrderId = $count === 1 ? $orders->first()->id : null;
            $singleOrder = $count === 1 ? $orders->first()->toArray() : null;
            if ($singleOrder) {
                $singleOrder['order_type_name'] = trans('orderType.'.$singleOrder['order_type']);
            }

            return response()->json(['count' => $count, 'single_order_id' => $singleOrderId, 'single_order' => $singleOrder]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch chef unviewed count', 'error' => $e->getMessage()], 500);
        }
    }
}
