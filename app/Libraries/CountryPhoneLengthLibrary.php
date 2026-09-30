<?php

namespace App\Libraries;

class CountryPhoneLengthLibrary
{
    /**
     * Standard national significant phone number length (digits, excluding
     * the calling code) per ISO 3166-1 alpha-3 country code, for the
     * countries most commonly used. Countries not listed fall back to a
     * length derived from the ITU-T E.164 15 digit maximum (see get()).
     *
     * @var array<string, int>
     */
    public const LENGTHS = [
        'USA' => 10, 'CAN' => 10, 'GBR' => 10, 'IND' => 10, 'PAK' => 10,
        'AUS' => 9, 'DEU' => 11, 'FRA' => 9, 'ITA' => 10, 'ESP' => 9,
        'CHN' => 11, 'JPN' => 10, 'BRA' => 11, 'RUS' => 10, 'ZAF' => 9,
        'ARE' => 9, 'SAU' => 9, 'EGY' => 10, 'NGA' => 10, 'KEN' => 9,
        'BGD' => 10, 'IDN' => 11, 'PHL' => 10, 'VNM' => 9, 'THA' => 9,
        'MYS' => 9, 'SGP' => 8, 'NZL' => 9, 'IRL' => 9, 'NLD' => 9,
        'BEL' => 9, 'CHE' => 9, 'SWE' => 9, 'NOR' => 8, 'DNK' => 8,
        'FIN' => 9, 'POL' => 9, 'PRT' => 9, 'GRC' => 10, 'TUR' => 10,
        'MEX' => 10, 'ARG' => 10, 'CHL' => 9, 'COL' => 10, 'PER' => 9,
        'KOR' => 10, 'HKG' => 8, 'TWN' => 9, 'ISR' => 9, 'QAT' => 8,
        'KWT' => 8, 'OMN' => 8, 'BHR' => 8, 'JOR' => 9, 'LBN' => 8,
        'IRQ' => 10, 'IRN' => 10, 'AFG' => 9, 'LKA' => 9, 'NPL' => 10,
        'MMR' => 9, 'KHM' => 9, 'LAO' => 9, 'UKR' => 9, 'ROU' => 9,
        'HUN' => 9, 'CZE' => 9, 'AUT' => 10, 'BGR' => 9, 'HRV' => 9,
        'ETH' => 9, 'GHA' => 9, 'TZA' => 9, 'UGA' => 9, 'MAR' => 9,
        'DZA' => 9, 'TUN' => 8, 'SDN' => 9, 'IRE' => 9, 'PRI' => 10,
        'JAM' => 10, 'TTO' => 10, 'VEN' => 10, 'ECU' => 9, 'BOL' => 8,
        'PRY' => 9, 'URY' => 8, 'CUB' => 8, 'DOM' => 10, 'GTM' => 8,
        'HND' => 8, 'SLV' => 8, 'NIC' => 8, 'CRI' => 8, 'PAN' => 8,
        'ISL' => 7, 'LUX' => 9, 'MLT' => 8, 'CYP' => 8, 'EST' => 8,
        'LVA' => 8, 'LTU' => 8, 'SVK' => 9, 'SVN' => 8, 'SRB' => 9,
        'BIH' => 8, 'MKD' => 8, 'ALB' => 9, 'MNE' => 8, 'BLR' => 9,
        'MDA' => 8, 'GEO' => 9, 'ARM' => 8, 'AZE' => 9, 'KAZ' => 10,
        'UZB' => 9, 'YEM' => 9, 'SYR' => 9, 'LBY' => 9, 'ZWE' => 9,
        'ZMB' => 9, 'MOZ' => 9, 'AGO' => 9, 'CMR' => 9, 'CIV' => 10,
        'SEN' => 9, 'MLI' => 8, 'NER' => 8, 'BFA' => 8, 'RWA' => 9,
        'SOM' => 8, 'MDG' => 9, 'MUS' => 8, 'NAM' => 9, 'BWA' => 8,
        'FJI' => 7, 'PNG' => 8, 'BRN' => 7, 'MAC' => 8, 'MNG' => 8,
    ];

    /**
     * Get the maximum number of digits for a country's national phone number.
     *
     * @param string $cca3 ISO 3166-1 alpha-3 country code
     * @param string $callingCode e.g. "+92"
     * @return int
     */
    public static function get(string $cca3, string $callingCode): int
    {
        if (isset(self::LENGTHS[$cca3])) {
            return self::LENGTHS[$cca3];
        }

        $digits = preg_replace('/\D/', '', $callingCode) ?? '';
        $max    = 15 - strlen($digits);

        return $max > 0 ? $max : 10;
    }
}
