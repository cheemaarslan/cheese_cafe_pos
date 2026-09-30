<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GoogleRecaptchaRequest;
use App\Services\GoogleRecaptchaService;
use Exception;

class GoogleRecaptchaController extends AdminController
{
    private GoogleRecaptchaService $googleRecaptchaService;

    public function __construct(GoogleRecaptchaService $googleRecaptchaService)
    {
        parent::__construct();
        $this->googleRecaptchaService = $googleRecaptchaService;

        $this->middleware(['permission:settings'])->only('update');
    }

    public function index()
    {
        try {
            return response()->json([
                'data' => $this->googleRecaptchaService->list()
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(GoogleRecaptchaRequest $request)
    {
        try {
            return response()->json([
                'data' => $this->googleRecaptchaService->update($request)
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
