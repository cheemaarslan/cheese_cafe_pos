<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use Smartisan\Settings\Facades\Settings;
use App\Http\Requests\GoogleAuthRequest;

class GoogleAuthService
{
    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('google_auth')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param GoogleAuthRequest $request
     * @return array
     * @throws Exception
     */
    public function update(GoogleAuthRequest $request)
    {
        try {
            $validated = $request->validated();
            // Ensure boolean value is stored properly if received as string "true"/"false" or "0"/"1"
            if (isset($validated['is_google_auth_enable'])) {
                $validated['is_google_auth_enable'] = filter_var($validated['is_google_auth_enable'], FILTER_VALIDATE_BOOLEAN);
            }
            Settings::group('google_auth')->set($validated);
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
