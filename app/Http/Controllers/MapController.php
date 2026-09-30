<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class MapController extends Controller
{
    public function staticMap(Request $request)
    {

        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $key = \Smartisan\Settings\Facades\Settings::group('site')->get('site_google_map_key');
        if (! $key) {
            $key = env('MIX_GOOGLE_MAP_KEY'); // fallback
        }

        Log::info('map', [
            'lat' => $lat,
            'lng' => $lng,
            'key' => $key,
        ]);

        $url = 'https://maps.googleapis.com/maps/api/staticmap?'.http_build_query([
            'center' => "{$lat},{$lng}",
            'zoom' => 15,
            'size' => '640x340',
            'scale' => 2,
            'markers' => "color:red|{$lat},{$lng}",
            'key' => $key,
        ]);

        $response = Http::get($url);
        if ($response->failed()) {
            return response()->json(['error' => 'API failed', 'status' => $response->status(), 'body' => $response->body(), 'url' => $url]);
        }

        return response($response->body())->header('Content-Type', 'image/png');
    }

    public function mapConfig(Request $request)
    {
        Log::info('working');
        $key = \Smartisan\Settings\Facades\Settings::group('site')->get('site_google_map_key');
        if (! $key) {
            $key = env('MIX_GOOGLE_MAP_KEY');
        }

        return response()->json(['key' => $key]);
    }
}
