<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Libraries\CountryPhoneLengthLibrary;
use App\Libraries\QueryExceptionLibrary;
use PragmaRX\Countries\Package\Countries;

class CountryCodeService
{

    /**
     * @throws Exception
     */
    public function list(): array
    {
        try {
            $countryArray = [];
            $countries    = Countries::all();
            foreach ($countries as $key => $country) {
                $countryArray[] = (object)[
                    'country_code' => $key,
                    'country_name' => $country['admin'] . ' (' . $key . ')',
                ];
            }
            return ['data' => $countryArray];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * List every country's calling code, flag and standard phone number
     * digit length, so the frontend can offer a full country picker for
     * phone number fields (e.g. guest login).
     *
     * @throws Exception
     */
    public function listPhoneCodes(): array
    {
        try {
            return ['data' => Cache::rememberForever('country_phone_codes_list', function () {
                $countryArray = [];
                $countries    = Countries::all();
                foreach ($countries as $key => $country) {
                    $callingCode = $country['calling_codes'][0] ?? null;
                    if (!$callingCode) {
                        continue;
                    }
                    $callingCode = $callingCode === '+1201' ? '+1' : $callingCode;

                    $countryArray[] = (object)[
                        'country_code' => $key,
                        'country_name' => $country['admin'],
                        'calling_code' => $callingCode,
                        'flag_emoji'   => $country['flag']['emoji'] ?? '',
                        'phone_length' => CountryPhoneLengthLibrary::get($key, $callingCode),
                    ];
                }
                return $countryArray;
            })];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show($country)
    {
        try {
            return Countries::where('cca3', $country)->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
