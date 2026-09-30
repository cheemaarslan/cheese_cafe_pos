<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactSubmissionRequest;
use App\Http\Resources\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleRecaptchaValidationService;

class ContactSubmissionController extends Controller
{
    public function store(ContactSubmissionRequest $request): \Illuminate\Http\Response | ContactSubmissionResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $recaptchaService = new GoogleRecaptchaValidationService();
            if (!$recaptchaService->validate($request->input('recaptcha_token'))) {
                return response(['status' => false, 'message' => 'Google reCAPTCHA verification failed.'], 422);
            }

            return new ContactSubmissionResource(ContactSubmission::create($request->validated()));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
