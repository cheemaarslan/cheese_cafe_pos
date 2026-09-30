<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Smartisan\Settings\Facades\Settings;

class GoogleAuthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::group('google_auth')->set([
            'is_google_auth_enable' => false,
            'client_id'     => env('GOOGLE_CLIENT_ID', ''),
            'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
            'redirect_url'  => env('APP_URL') . '/auth/google/callback',
        ]);
    }
}
