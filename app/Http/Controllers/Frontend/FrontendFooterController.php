<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;

class FrontendFooterController extends Controller
{
    public function index()
    {
        $timeSlots = TimeSlot::all();

        $footerImage = ThemeSetting::where('key', 'theme_footer_image')->first();

        $developerInfo = [
            'name' => 'Bitlogicx',
            'url' => 'https://bitlogicx.com',
        ];
        
        // Get stats from settings
        $statYears = \Smartisan\Settings\Facades\Settings::group('site')->get('site_stat_years', 5);
        $statAwards = \Smartisan\Settings\Facades\Settings::group('site')->get('site_stat_awards', 7);
        $statLocations = \Smartisan\Settings\Facades\Settings::group('site')->get('site_stat_locations', 10);

        return response()->json([
            'status' => true,
            'data' => [
                'time_slots' => $timeSlots,
                'theme_footer_image' => $footerImage && $footerImage->value
                    ? asset($footerImage->value)
                    : asset('images/default/footer_bg.webp'),
                'stats' => [
                    'years' => (int) $statYears,
                    'awards' => (int) $statAwards,
                    'locations' => (int) $statLocations,
                ],
                'developer_info' => $developerInfo,
            ]
        ]);
    }
}
