<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Exception;

class NotificationController extends AdminController
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->middleware(['permission:settings'])->only('update');
    }

    public function index(
    )
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $perPage = (int) request()->get('per_page', 20);
            $page = (int) request()->get('page', 1);
            $notifications = $user->notifications()
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            return response([
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'last_page' => $notifications->lastPage(),
                ],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    // readAll function 
    public function readAll()
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $user->unreadNotifications()->update(['read_at' => now()]);

            return response(['status' => true, 'message' => 'All notifications marked as read.']);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(NotificationRequest $request
    ) : \Illuminate\Http\Response | NotificationResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new NotificationResource($this->notificationService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
