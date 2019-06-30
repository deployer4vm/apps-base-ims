<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" class="default-style layout-fixed layout-navbar-fixed">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('appconfig.system.title') }}</title>

    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    @if(config('appconfig.system.frontend_admin.assets_admintemplate.font.fontawesome'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/fontawesome.css') }}">
    @endif
    @if(config('appconfig.system.frontend_admin.assets_admintemplate.font.ionicons'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/ionicons.css') }}">
    @endif
    @if(config('appconfig.system.frontend_admin.assets_admintemplate.font.linearicons'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/linearicons.css') }}">
    @endif
    @if(config('appconfig.system.frontend_admin.assets_admintemplate.font.open-iconic'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/open-iconic.css') }}">
    @endif
    @if(config('appconfig.system.frontend_admin.assets_admintemplate.font.pe-icon-7-stroke'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/pe-icon-7-stroke.css') }}">
    @endif
    
    <link href="{{ asset('/dist/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/appwork.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/theme-app.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/colors.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/uikit.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/style.css') }}" rel="stylesheet">
    
    @if(config('appconfig.system.frontend_admin.assets_link'))
    @foreach (config('appconfig.system.frontend_admin.assets_link') as $value)
    <link rel="stylesheet" href="{{ asset($value) }}">
    @endforeach
    @endif

</head>
<body>

    <div id="app"></div>

    @if(config('appconfig.system.frontend_admin.assets_js'))
    @foreach (config('appconfig.system.frontend_admin.assets_js') as $value)
    <script src="{{ asset($value) }}"></script>
    @endforeach
    @endif

    <!-- Layout helpers -->
    <script src="{{ asset('/dist/vendor/js/layout-helpers.js') }}"></script>
    <script src="{{ asset('/dist/app.js') }}"></script>

</body>
</html>
