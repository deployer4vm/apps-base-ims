<!-- Layout navbar -->
<nav class="layout-navbar navbar navbar-expand-lg navbar-light bg-primary navbar-theme align-items-lg-center container-p-x" id="layout-navbar">

    <!-- Brand saat mode mobile (tidak ada sidebar) -->
    <a href="/" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
        <span class="app-brand-logo demo bg-primary">
            <img style="max-height: 30px; max-width: 60px;" src="{{asset('/assets/images/logo.png')}}" />
        </span>
        <span class="app-brand-text demo font-weight-normal ml-2">{{config('AppConfig.system.template.admin.title')}}</span>
    </a>
    <!-- <a href="{{ route('dashboard') }}" class="navbar-brand">
        
    </a> -->

    @empty($hide_layout_sidenav_toggle)
    <!-- Sidenav toggle (see resources/assets/css/demo.css) -->
    <div class="layout-sidenav-toggle navbar-nav d-lg-none align-items-lg-center mr-auto">
        <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)">
            <i class="ion ion-md-menu text-large align-middle"></i>
        </a>
    </div>
    <!-- <div class="layout-sidenav-toggle navbar-nav align-items-lg-center mr-auto mr-lg-4">
        <a class="nav-item nav-link px-0 ml-2" href="javascript:void(0)">
            <i class="ion ion-md-menu text-large align-middle"></i>
        </a>
    </div> -->
    @endempty

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse collapse" id="layout-navbar-collapse">
        <!-- Divider -->
        <hr class="d-lg-none w-100 my-2">

        <!-- Search -->
        <!-- <div class="navbar-nav align-items-lg-center">
            <label class="nav-item navbar-text navbar-search-box p-0 active">
                <i class="ion ion-ios-search navbar-icon align-middle"></i>
                <span class="navbar-search-input pl-2">
                    <input type="text" class="form-control navbar-text mx-2" placeholder="Search..." style="width:200px">
                </span>
            </label>
        </div> -->
        <div class="navbar-nav align-items-lg-center ml-auto">

<?php /* 
            <div class="demo-navbar-notifications nav-item dropdown mr-lg-3">
                <a class="nav-link dropdown-toggle hide-arrow" href="#" data-toggle="dropdown">
                    <i class="ion ion-md-notifications-outline navbar-icon align-middle"></i>
                    <span class="badge badge-primary badge-dot indicator"></span>
                    <span class="d-lg-none align-middle">&nbsp; Notifications</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="bg-primary text-center text-white font-weight-bold p-3">
                        4 New Notifications
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action media d-flex align-items-center">
                            <div class="ui-icon ui-icon-sm ion ion-md-home bg-secondary border-0 text-white"></div>
                            <div class="media-body line-height-condenced ml-3">
                                <div class="text-body">Login from 192.168.1.1</div>
                                <div class="text-light small mt-1">
                                    Aliquam ex eros, imperdiet vulputate hendrerit et.
                                </div>
                                <div class="text-light small mt-1">12h ago</div>
                            </div>
                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action media d-flex align-items-center">
                            <div class="ui-icon ui-icon-sm ion ion-md-person-add bg-info border-0 text-white"></div>
                            <div class="media-body line-height-condenced ml-3">
                                <div class="text-body">You have <strong>4</strong> new followers</div>
                                <div class="text-light small mt-1">
                                    Phasellus nunc nisl, posuere cursus pretium nec, dictum vehicula tellus.
                                </div>
                            </div>
                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action media d-flex align-items-center">
                            <div class="ui-icon ui-icon-sm ion ion-md-power bg-danger border-0 text-white"></div>
                            <div class="media-body line-height-condenced ml-3">
                                <div class="text-body">Server restarted</div>
                                <div class="text-light small mt-1">
                                    19h ago
                                </div>
                            </div>
                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action media d-flex align-items-center">
                            <div class="ui-icon ui-icon-sm ion ion-md-warning bg-warning border-0 text-body"></div>
                            <div class="media-body line-height-condenced ml-3">
                                <div class="text-body">99% server load</div>
                                <div class="text-light small mt-1">
                                    Etiam nec fringilla magna. Donec mi metus.
                                </div>
                                <div class="text-light small mt-1">
                                    20h ago
                                </div>
                            </div>
                        </a>
                    </div>

                    <a href="javascript:void(0)" class="d-block text-center text-light small p-2 my-1">Show all notifications</a>
                </div>
            </div>
*/ ?>            

            <!-- Divider -->
            <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>

            <div class="demo-navbar-user nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                        <!-- <img src="/img/avatars/1.png" alt class="d-block ui-w-30 rounded-circle"> -->
                        <div class="avatar-header-block d-block rounded-circle text-center">
                            <i class="ion ion-ios-person"></i>
                        </div>
                        <span class="px-1 mr-lg-2 ml-2 ml-lg-0">{{ \UserAuth::user('name') }}</span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                     
                    <a href="{{ \UserAuth::is('pegawai_ptt')?route('pegawai.profile'):route('user.profile') }}" class="dropdown-item">
                        <i class="ion ion-md-settings text-lightest"></i> &nbsp; My Profile
                    </a>

                    <?php 
                    $uRoles = UserAuth::role();
                    if($uRoles && count($uRoles)>1){ 
                    ?>
                    <div class="dropdown-divider"></div>
                    <?php 
                        foreach ($uRoles as $key => $value) {
                            if($value['role_code']==UserAuth::getActiveUserRoleCode()){ 
                    ?>
                    <div class="dropdown-item">
                        <i class="ion ion-md-radio-button-on text-lightest"></i> &nbsp; <b>{{$value['name']}}</b>
                    </div>
                    <?php }else{ ?>
                    
                    <a href="{{ route('user.changerole',['role_code'=>$key]) }}" class="dropdown-item">
                        <i class="ion ion-md-radio-button-off text-lightest"></i> &nbsp; {{$value['name']}}
                    </a>
                    <?php }}} ?>

                    <div class="dropdown-divider"></div>
                    <a href="{{route('auth.logout')}}" class="dropdown-item">
                        <i class="ion ion-ios-log-out text-danger"></i> &nbsp; {{__('auth.logout')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- / Layout navbar -->
