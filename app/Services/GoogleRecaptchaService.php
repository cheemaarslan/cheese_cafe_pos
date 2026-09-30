<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use Smartisan\Settings\Facades\Settings;
use App\Http\Requests\GoogleRecaptchaRequest;

class GoogleRecaptchaService
{
    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('google_recaptcha')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param GoogleRecaptchaRequest $request
     * @return array
     * @throws Exception
     */
    public function update(GoogleRecaptchaRequest $request)
    {
        try {
            $validated = $request->validated();
            if (isset($validated['is_google_recaptcha_enable'])) {
                $validated['is_google_recaptcha_enable'] = filter_var($validated['is_google_recaptcha_enable'], FILTER_VALIDATE_BOOLEAN);
            }
            Settings::group('google_recaptcha')->set($validated);
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
