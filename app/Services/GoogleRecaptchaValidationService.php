<?php

namespace App\Services;

use Smartisan\Settings\Facades\Settings;
use Illuminate\Support\Facades\Http;

class GoogleRecaptchaValidationService
{
    public function validate($token)
    {
        $settings = Settings::group('google_recaptcha')->all();
        
        $isEnabled = $settings['is_google_recaptcha_enable'] ?? false;
        
        // If not enabled, return true automatically
        if (!$isEnabled) {
            return true;
        }

        $secretKey = $settings['private_key'] ?? '';
        
        if (empty($secretKey)) {
            // Misconfigured but enabled, we might fail open or closed. Failing closed is safer.
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
            ]);
            
            $body = $response->json();
            
            if (isset($body['success']) && $body['success'] === true) {
                // If using v3, we might want to check the score, but v2/v3 both return success=true
                if (isset($settings['version']) && $settings['version'] === 'v3') {
                    if (isset($body['score']) && $body['score'] >= 0.5) {
                        return true;
                    }
                    return false;
                }
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
