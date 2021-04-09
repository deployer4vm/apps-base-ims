@extends('layouts.web_admin.application')

@section('layout-content')
<!-- Layout wrapper -->
<div class="layout-wrapper {{config('AppConfig.system.web_admin.sidenav_horizontal',1)==1?'layout-1 layout-without-sidenav':'layout-2'}}">
    <div class="layout-inner">

        <!-- Layout sidenav - Sidebar  -->
        @if(config('AppConfig.system.web_admin.sidenav_horizontal',1)!=1)
            @include('layouts.web_admin.includes.layout-sidenav', ['layout_sidenav_horizontal' => false])
        @endif

        <!-- Layout navbar - Topbar -->
        @if(config('AppConfig.system.web_admin.sidenav_horizontal',1)==1)
            @include('layouts.web_admin.includes.layout-navbar', ['hide_layout_sidenav_toggle' => true])
        @endif

        <!-- Layout container -->
        <div class="layout-container">
            <!-- Layout navbar - Sidebar -->
            @if(config('AppConfig.system.web_admin.sidenav_horizontal',1)!=1)
                @include('layouts.web_admin.includes.layout-navbar', ['hide_layout_sidenav_toggle' => false])
            @endif

            <!-- Layout content -->
            <div class="layout-content">

                <!-- Layout sidenav - Topbar -->
                @if(config('AppConfig.system.web_admin.sidenav_horizontal',1)==1)
                    @include('layouts.web_admin.includes.layout-sidenav', ['layout_sidenav_horizontal' => true])
                @endif

                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    @yield('content')
                </div>
                <!-- / Content -->

                <!-- Layout footer -->
                @include('layouts.web_admin.includes.layout-footer')
            </div>
            <!-- Layout content -->

        </div>
        <!-- / Layout container -->

    </div>
</div>
<!-- / Layout wrapper -->
@endsection