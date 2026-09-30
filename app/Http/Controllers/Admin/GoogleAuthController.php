<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GoogleAuthRequest;
use App\Services\GoogleAuthService;
use Exception;

class GoogleAuthController extends AdminController
{
    private GoogleAuthService $googleAuthService;

    public function __construct(GoogleAuthService $googleAuthService)
    {
        parent::__construct();
        $this->googleAuthService = $googleAuthService;

        $this->middleware(['permission:settings'])->only('update');
    }

    public function index()
    {
        try {
            return response()->json([
                'data' => $this->googleAuthService->list()
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(GoogleAuthRequest $request)
    {
        try {
            return response()->json([
                'data' => $this->googleAuthService->update($request)
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
