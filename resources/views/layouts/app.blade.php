<!DOCTYPE html>
@php
    $settings = App\Models\Setting::pluck('value','key')->toArray();
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | {{ $settings['application_name'] }}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ $settings['favicon'] }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- General CSS Files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party.css') }}">
    @if(!Auth::user()->dark_mode)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins.css') }}">
        <link href="{{ mix('assets/css/full-screen.css') }}" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('front_web/css/custom.css') }}">
    @else
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.dark.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins.dark.css') }}">
        <link rel="stylesheet" type="text/css" href="{{asset('front_web/css/flatpicker-dark.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('front_web/css/custom-dark.css') }}">
    @endif

{{--    <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.css') }}">--}}
{{--    <link rel="stylesheet" type="text/css" href="{{ asset('css/styles.css') }}">--}}


{{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">--}}
{{--    <link href="{{ mix('assets/css/custom.css') }}" rel="stylesheet" type="text/css "/>--}}

    <!-- CSS Libraries -->
    @yield('page_css')
    @yield('css')
    @livewireStyles

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js"
            data-turbolinks-eval="false" data-turbo-eval="false"></script>
    @routes
   <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script src="{{ asset('assets/js/third-party.js') }}"></script>
    <script src="{{ asset('ckeditor4/ckeditor.js') }}"></script>

    <script data-turbo-eval="false">
        let stripe = ''
        @if (config('services.stripe.key'))
            stripe = Stripe('{{ config('services.stripe.key') }}')
        @endif
        let changePasswordUrl = "{{ route('user.changePassword') }}"
        let darkMode = "{{ Auth::user()->dark_mode ?  1 : 0 }}"
        let lang = "{{ Auth::user()->language ?? 'en' }}"
        let defaultImage = "{{ asset('front_web/images/default.jpg') }}"
        // Lang.setLocale(lang)
    </script>
    <script src="{{ asset('messages.js') }}"></script>
    <script src="{{ mix('assets/js/pages.js') }}"></script>
</head>
@php $styleCss='style' @endphp
<body class="">
<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-row flex-column-fluid">
        @include('layouts.sidebar')
        <div class="wrapper d-flex flex-column flex-row-fluid">
            <div class='container-fluid d-flex align-items-stretch justify-content-between px-0'>
                @include('layouts.header')
            </div>

            <div class="content d-flex flex-column flex-column-fluid pt-7">
                @yield('header_toolbar')
                <div class='d-flex flex-wrap flex-column-fluid'>
                    @yield('content')
                </div>
            </div>
            <div class='container-fluid footer'>
                @include('layouts.footer')
            </div>
        </div>
    </div>
</div>

@include('profile.changePassword')
</body>
</html>
