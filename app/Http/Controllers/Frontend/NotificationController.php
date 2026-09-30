<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->limit(20)->get();
        return response()->json(['data' => $notifications]);
    }

    public function read($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read.']);
        }
        return response()->json(['message' => 'Notification not found.'], 404);
    }

    public function readAll()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['message' => 'All notifications marked as read.']);
    }
}
