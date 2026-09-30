<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@php
    $site = \Smartisan\Settings\Facades\Settings::group('site')->all();
    $siteTitle = $site['site_meta_title'] ?? $site['site_og_title'] ?? \Smartisan\Settings\Facades\Settings::group('company')->get('company_name');
    $siteDescription = $site['site_meta_description'] ?? $site['site_og_description'] ?? \Smartisan\Settings\Facades\Settings::group('company')->get('company_address');
    $siteKeywords = $site['site_meta_keywords'] ?? '';
    $siteOgImage = $site['site_og_image'] ?? ($favicon ?? '');
    $canonicalUrl = url()->current();
@endphp

<head>
    <!-- REQUIRED META TAGS -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $siteDescription }}">
    @if (!blank($siteKeywords))
        <meta name="keywords" content="{{ $siteKeywords }}">
    @endif
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if (!blank($siteOgImage))
        <meta property="og:image" content="{{ asset($siteOgImage) }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ asset($siteOgImage) }}">
    @endif
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $siteDescription }}">

    @php
        $sliders = \App\Models\Slider::where('status', \App\Enums\Status::ACTIVE)->orderBy('id', 'desc')->get();
        $initialSliders = $sliders->map(function ($slider) {
            return [
                'id' => $slider->id,
                'image' => $slider->image,
            ];
        })->values()->toArray();
        $firstSlider = $sliders->first();
        $themeLogo = \App\Models\ThemeSetting::where(['key' => 'theme_logo'])->first()?->logo;
    @endphp
    @if(!blank($themeLogo))
        <link rel="preload" as="image" href="{{ $themeLogo }}">
    @endif
    @if($firstSlider)
        <link rel="preload" as="image" type="image/webp" href="{{ $firstSlider->image }}" fetchpriority="high">
    @endif
    <script>
        window.initialSliders = @json($initialSliders);
    </script>

    <!-- PRELOAD CRITICAL FONTS -->
    <link rel="preload" href="{{ asset('themes/default/fonts/fontawesome/fa-solid-900.woff2') }}" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('themes/default/fonts/fontawesome/fa-brands-400.woff2') }}" as="font"
        type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('themes/default/fonts/lab/lab.woff2?1740891535263') }}" as="font"
        type="font/woff2" crossorigin>

    <!-- FONTS -->
    <link rel="preload" href="{{ asset('themes/default/fonts/fontawesome/fontawesome.css?v=2') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('themes/default/fonts/fontawesome/fontawesome.css?v=2') }}">
    </noscript>

    <link rel="preload" href="{{ asset('themes/default/fonts/lab/lab.css?v=2') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('themes/default/fonts/lab/lab.css?v=2') }}">
    </noscript>

    <link rel="preload" href="{{ asset('themes/default/fonts/typography/public/public.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('themes/default/fonts/typography/public/public.css') }}">
    </noscript>

    <link rel="preload" href="{{ asset('themes/default/fonts/typography/rubik/rubik.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('themes/default/fonts/typography/rubik/rubik.css') }}">
    </noscript>

    <!-- CUSTOM STYLE -->
    <link rel="preload" href="{{ asset('themes/default/css/custom.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
    </noscript>

    <link rel="preload" href="{{ mix('css/app.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </noscript>
    <!-- PAGE TITLE -->
    <title>{{ $siteTitle }}</title>

    <!-- FAV ICON -->
    <link rel="icon" type="image" href="{{ $favicon }}">


    @if (!blank($analytics))
        @foreach ($analytics as $analytic)
            @if (!blank($analytic->analyticSections))
                @foreach ($analytic->analyticSections as $section)
                    @if ($section->section == \App\Enums\AnalyticSection::HEAD)
                        {!! $section->data !!}
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif
</head>

<body>
    @if (!blank($analytics))
        @foreach ($analytics as $analytic)
            @if (!blank($analytic->analyticSections))
                @foreach ($analytic->analyticSections as $section)
                    @if ($section->section == \App\Enums\AnalyticSection::BODY)
                        {!! $section->data !!}
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif

    <div id="app">
        <default-component />
    </div>

    @if (!blank($analytics))
        @foreach ($analytics as $analytic)
            @if (!blank($analytic->analyticSections))
                @foreach ($analytic->analyticSections as $section)
                    @if ($section->section == \App\Enums\AnalyticSection::FOOTER)
                        {!! $section->data !!}
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif

    <script>
        const APP_URL = "{{ env('MIX_HOST') }}";
        const APP_KEY = "{{ env('MIX_API_KEY') }}";
        const GOOGLE_TOKEN = "{{ env('MIX_GOOGLE_MAP_KEY') }}";
        const APP_DEMO = "{{ env('MIX_DEMO') }}";
    </script>

    <script defer src="{{ mix('js/app.js') }}"></script>
    <script defer src="{{ asset('themes/default/js/drawer.js') }}"></script>
    <script defer src="{{ asset('themes/default/js/modal.js') }}"></script>
    <script defer src="{{ asset('themes/default/js/customScript.js') }}"></script>
    <script defer src="{{ asset('themes/default/js/tabs.js') }}"></script>
    <script defer src="{{ asset('themes/default/js/dropdown.js') }}"></script>


</body>

</html>