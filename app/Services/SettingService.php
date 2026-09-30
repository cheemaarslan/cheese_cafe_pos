<?php

namespace App\Services;

use Smartisan\Settings\Facades\Settings;

class SettingService
{
    public function list() : array
    {
        $array = [];
        $array = array_merge($array, Settings::group('company')->all());
        $array = array_merge($array, Settings::group('site')->all());
        $array = array_merge($array, Settings::group('theme')->all());
        $array = array_merge($array, Settings::group('otp')->all());
        $array = array_merge($array, Settings::group('social_media')->all());
        $array = array_merge($array, Settings::group('order_setup')->all());
        $array = array_merge($array, Settings::group('notification')->all());
        $array = array_merge($array, Settings::group('cookies')->all());
        
        // Expose only the enable flag, not the secrets
        $googleAuth = Settings::group('google_auth')->all();
        $array['is_google_auth_enable'] = $googleAuth['is_google_auth_enable'] ?? false;
        
        // Expose public reCAPTCHA settings (never expose private key)
        $googleRecaptcha = Settings::group('google_recaptcha')->all();
        $array['is_google_recaptcha_enable'] = $googleRecaptcha['is_google_recaptcha_enable'] ?? false;
        $array['google_recaptcha_site_key'] = $googleRecaptcha['site_key'] ?? '';
        $array['google_recaptcha_version'] = $googleRecaptcha['version'] ?? '';
        
        return $array;
    }
}
