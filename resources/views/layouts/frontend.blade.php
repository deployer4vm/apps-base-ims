<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="default-style">
    <head>
        <title>{{ config('AppConfig.system.template.frontend.title') }}</title>

        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
        <link rel="icon" type="image/x-icon" href="favicon.ico">

        <!-- Icon fonts -->
        @if(config('AppConfig.system.template.frontend.assets_font.fontawesome'))
        <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/fontawesome.css') }}">
        @endif
        @if(config('AppConfig.system.template.frontend.assets_font.ionicons'))
        <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/ionicons.css') }}">
        @endif
        @if(config('AppConfig.system.template.frontend.assets_font.linearicons'))
        <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/linearicons.css') }}">
        @endif
        @if(config('AppConfig.system.template.frontend.assets_font.open-iconic'))
        <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/open-iconic.css') }}">
        @endif
        @if(config('AppConfig.system.template.frontend.assets_font.pe-icon-7-stroke'))
        <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/pe-icon-7-stroke.css') }}">
        @endif

        <!-- Core stylesheets -->
        <link href="{{ asset('/dist/css/bootstrap.css') }}" rel="stylesheet">
        <link href="{{ asset('/dist/css/appwork.css') }}" rel="stylesheet">
        <link href="{{ asset('/dist/css/theme-app.css') }}" rel="stylesheet">
        <link href="{{ asset('/dist/css/colors.css') }}" rel="stylesheet">
        <link href="{{ asset('/dist/css/uikit.css') }}" rel="stylesheet">

        @if(config('AppConfig.system.template.frontend.assets_link'))
        @foreach (config('AppConfig.system.template.frontend.assets_link') as $value)
        <link rel="stylesheet" href="{{ asset($value) }}">
        @endforeach
        @endif
        
        <!-- Core scripts -->
        <script src="{{ asset('/dist/vendor/webjs/pace.js') }}"></script>

        <!-- Page -->
        <!-- <link rel="stylesheet" href="{{asset('/dist/css/authentication.css')}}"> -->
    </head>
    <body>
        <!-- Pace.js loader -->
        <div class="page-loader"><div class="bg-primary"></div></div>

        <!-- Content -->
        @yield('content')
        <!-- / Content -->

        @if(config('AppConfig.system.template.frontend.assets_js'))
        @foreach (config('AppConfig.system.template.frontend.assets_js') as $value)
        <script src="{{ asset($value) }}"></script>
        @endforeach
        @endif

        <!-- Core scripts -->
        <script src="{{ asset('/dist/vendor/weblibs/jquery/3.2.1/jquery.min.js') }}"></script>
        <script src="{{ asset('/dist/vendor/weblibs/popper/popper.js') }}"></script>
        <script src="{{ asset('/dist/vendor/webjs/bootstrap.js') }}"></script>
    </body>
</html>