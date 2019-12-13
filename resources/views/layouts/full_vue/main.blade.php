<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="default-style layout-fixed layout-navbar-fixed">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('AppConfig.system.template.admin.title') }}</title>

    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    @if(config('AppConfig.system.web_admin.assets_template.font.fontawesome'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/fontawesome.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.ionicons'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/ionicons.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.linearicons'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/linearicons.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.open-iconic'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/open-iconic.css') }}">
    @endif
    @if(config('AppConfig.system.web_admin.assets_template.font.pe-icon-7-stroke'))
    <link rel="stylesheet" href="{{ asset('/dist/vendor/fonts/pe-icon-7-stroke.css') }}">
    @endif
    
    <link href="{{ asset('/dist/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/appwork.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/theme-app.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/colors.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/uikit.css') }}" rel="stylesheet">
    <link href="{{ asset('/dist/css/style.css') }}" rel="stylesheet">
    
    @if(config('AppConfig.system.web_admin.assets_link'))
    @foreach (config('AppConfig.system.web_admin.assets_link') as $value)
    <link rel="stylesheet" href="{{ asset($value) }}">
    @endforeach
    @endif

    <style>
    .app-splash-screen {
      background: #fff;
      position: fixed;
      display: block;
      z-index: 99999999;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      opacity: 1;
      transition: opacity .3s;
    }

    .app-splash-screen-content {
        text-align: center;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-animation: appSplashScreenAnimation 1.2s ease-in-out 0s infinite;
        animation: appSplashScreenAnimation 1.2s ease-in-out 0s infinite;
    }

    .app-splash-screen-content .logo {
        max-width: 70px; max-height: 70px;
    }

    @-webkit-keyframes appSplashScreenAnimation {
      0%,
      20% {
        -webkit-transform: translate(-50%, -50%) rotateY(0);
        transform: translate(-50%, -50%) rotateY(0);
      }
      50% {
        -webkit-transform: translate(-50%, -50%) rotateY(180deg);
        transform: translate(-50%, -50%) rotateY(180deg);
      }
      80%,
      100% {
        -webkit-transform: translate(-50%, -50%) rotateY(360deg);
        transform: translate(-50%, -50%) rotateY(360deg);
      }
    }

    @keyframes appSplashScreenAnimation {
      0%,
      20% {
        -webkit-transform: translate(-50%, -50%) rotateY(0);
        transform: translate(-50%, -50%) rotateY(0);
      }
      50% {
        -webkit-transform: translate(-50%, -50%) rotateY(180deg);
        transform: translate(-50%, -50%) rotateY(180deg);
      }
      80%,
      100% {
        -webkit-transform: translate(-50%, -50%) rotateY(360deg);
        transform: translate(-50%, -50%) rotateY(360deg);
      }
    }
  </style>
</head>
<body>

    <!-- Splash screen -->
    <div class="app-splash-screen">
        <div class="app-splash-screen-content">
            @if(config('AppConfig.system.template.logo'))<img class="logo" src="{{asset(config('AppConfig.system.template.logo'))}}">@endif
            <div class="text-large font-weight-bolder">{{ config('AppConfig.system.template.admin.title') }}</div>
        </div>
    </div>
    <!-- / Splash screen -->

    <div id="app"></div>

    @if(config('AppConfig.system.web_admin.assets_js'))
    @foreach (config('AppConfig.system.web_admin.assets_js') as $value)
    <script src="{{ asset($value) }}"></script>
    @endforeach
    @endif

    <!-- Layout helpers -->
    <script src="{{ asset('/dist/vendor/js/layout-helpers.js') }}"></script>
    <script src="{{ mix('/dist/app.js') }}"></script>

</body>
</html>
