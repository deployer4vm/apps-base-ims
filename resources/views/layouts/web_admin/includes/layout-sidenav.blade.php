<?php 
$sidebarMenu = config('AppConfig.sidenav',[]);
$routeName = Route::currentRouteName();
function isInGroup($curGroup){
    return true;
}
?>
<div id="layout-sidenav" class="{{ !empty($layout_sidenav_horizontal) ? 'layout-sidenav-horizontal sidenav-horizontal container-p-x flex-grow-0' : 'layout-sidenav sidenav-vertical' }} sidenav bg-sidenav-theme">

    <!-- Brand saat mode desktop (di sidebar) atau saat menu tampil di mobile -->
    @if(empty($layout_sidenav_horizontal))
    <div class="app-brand demo sidenav-app-brand">
        <!-- <span class="app-brand-logo demo bg-primary">
            <svg viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="a" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse"><stop stop-opacity=".25" offset="0"></stop><stop stop-opacity=".1" offset=".3"></stop><stop stop-opacity="0" offset=".9"></stop></linearGradient><linearGradient id="e" x1="76.9" x2="92.64" y1="26.38" y2="31.49" xlink:href="#a"></linearGradient><linearGradient id="d" x1="107.12" x2="122.74" y1="53.41" y2="48.33" xlink:href="#a"></linearGradient></defs><path style="fill: #fff;" transform="translate(-.1)" d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"></path><path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z" fill="url(#a)"></path><path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z" fill="url(#e)"></path><path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z" fill="url(#d)"></path></svg>
        </span>-->
        
        <span class="app-brand-logo demo disidenav">
            <img style="max-height: 30px; max-width: 60px;" src="{{asset('/assets/images/logo.png')}}" />
        </span>

        <!-- burger menu saat sidebar menutup -->
        <!-- <a href="javascript:void(0)" class="sidenav-button-onhover sidenav-link">
            <i class="ion ion-md-menu align-middle"></i>
        </a> -->

        <a href="{{route('dashboard')}}" class="app-brand-text demo sidenav-text font-weight-normal ml-2">
            {{config('AppConfig.system.template.admin.title')}}
        </a>

        <!-- burger menu saat sidebar membuka -->
        <a
            href="javascript:void(0)"
            class="layout-sidenav-toggle sidenav-link text-large ml-auto"
        >
            <i class="ion ion-md-menu align-middle"></i>
        </a>

    </div>
    @endif

    <div class="sidenav-divider mt-0"></div>

    <!-- Inner -->
    <ul class="sidenav-inner{{ empty($layout_sidenav_horizontal) ? ' py-1' : '' }}">

        <li class="sidenav-item{{ Request::is('/') ? ' active' : '' }}">
            <a href="{{ route('dashboard') }}" class="sidenav-link"><i class="sidenav-icon ion ion-ios-speedometer"></i><div>Dashboard</div></a>
        </li>

        <?php foreach($sidebarMenu as $packageNamespace => $menus){ ?>
        <?php if($menus['has_acl']==0 || ($menus['has_access'] && ($menus['tenant_group_id']==0 || isInGroup($menus['tenant_group_id'])))) { ?>

        <?php } ?>
        <?php } ?>

        <!-- Setup -->
        @if(false)
        <!-- \UserAuth::hasAccess('Master')) -->
        <li class="sidenav-item{{ strpos($routeName, 'master') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-build"></i><div>Setup</div></a>

            <ul class="sidenav-menu">
                
                @if(\UserAuth::hasAccess('Master.jenisijin'))
                <li class="sidenav-item{{ strpos($routeName, 'master.jenis_ijin') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.jenis_ijin.list') }}" class="sidenav-link"><div>Jenis Permohonan</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.shift'))
                <li class="sidenav-item{{ strpos($routeName, 'master.shift') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.shift.list') }}" class="sidenav-link"><div>Shift</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.jabatan'))
                <li class="sidenav-item{{ strpos($routeName, 'master.jabatan') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.jabatan.list') }}" class="sidenav-link"><div>Jabatan</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.instansi'))
                <li class="sidenav-item{{ strpos($routeName, 'master.instansi') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.instansi.list') }}" class="sidenav-link"><div>Satuan Kerja</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.harilibur'))
                <li class="sidenav-item{{ strpos($routeName, 'master.hari_libur') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.hari_libur.list') }}" class="sidenav-link"><div>Hari Libur</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.mesinabsen'))
                <li class="sidenav-item{{ strpos($routeName, 'master.mesin_absen') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.mesin_absen.list') }}" class="sidenav-link"><div>Mesin Absen</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('moduser'))
                <li class="sidenav-item{{ strpos($routeName, 'user.') === 0 ? ' active' : '' }}">
                    <a href="{{ route('user.list') }}" class="sidenav-link"><div>Manajemen User</div></a>
                </li>
                @endif
               
            </ul>
        </li>
        @endif


        <!-- Pages 
        <li class="sidenav-item{{ strpos($routeName, 'pages.') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon ion ion-md-document"></i>
                <div>Kepegawaian</div>
            </a>
            <ul class="sidenav-menu">

                <li class="sidenav-item{{ strpos($routeName, 'pages.articles.') === 0 ? ' active open' : '' }}">
                    <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><div>Biografi</div></a>

                    <ul class="sidenav-menu">
                        <li class="sidenav-item{{ $routeName == 'pages.articles.list' ? ' active' : '' }}">
                            <a href="#" class="sidenav-link"><div>Data Alamat</div></a>
                        </li>
                        <li class="sidenav-item{{ $routeName == 'pages.articles.edit' ? ' active' : '' }}">
                            <a href="#" class="sidenav-link"><div>Riwayat Pendidikan</div></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
-->

        <!-- Kepegawaian -->
        @if(false)
        <!-- \UserAuth::hasAccess('Pegawai')) -->
        <li class="sidenav-item{{ strpos($routeName, 'pegawai') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-contact"></i><div>Kepegawaian</div></a>

            <ul class="sidenav-menu">
                @if(\UserAuth::hasAccess('Pegawai.profile'))
                <li class="sidenav-item{{ $routeName == 'pegawai.profile' ? ' active' : '' }}">
                    <a href="{{ route('pegawai.profile') }}" class="sidenav-link"><div>Data Pribadi</div></a>
                </li>
                @endif
              <!--  <li class="sidenav-item{{ $routeName == 'dashboards.dashboard-2' ? ' active' : '' }}">
                    <a href="{{ route('permohonan_absen.index') }}" class="sidenav-link"><div>Data Alamat</div></a>
                </li>
                <li class="sidenav-item{{ $routeName == 'dashboards.dashboard-3' ? ' active' : '' }}">
                    <a href="#" class="sidenav-link"><div>Riwayat Pendidikan</div></a>
                </li>
                <li class="sidenav-item{{ $routeName == 'dashboards.dashboard-4' ? ' active' : '' }}">
                    <a href="#" class="sidenav-link"><div>Riwayat Jabatan</div></a>
                </li> --> 
                <!-- khusus utk non pegawai muncul ini -->
                @if(\UserAuth::hasAccess('Pegawai.master'))
                <li class="sidenav-item{{ strpos($routeName, 'pegawai') === 0 && $routeName != 'pegawai.profile' ? ' active' : '' }}">
                    <a href="{{ route('pegawai.list') }}" class="sidenav-link"><div>Daftar Pegawai</div></a>
                </li>
                @endif
               
            </ul>
        </li>
        @endif
        
        @if(false)
        <!-- \UserAuth::hasAccess('Absensi')) -->
        <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen') === 0 ? ' active open' : '' }}">

            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-finger-print"></i><div>Absensi</div></a>
            <ul class="sidenav-menu">
                @if(\UserAuth::hasAccess('Absensi.permohonan'))
                <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen') === 0 && strpos($routeName, 'permohonan_absen.approval') !== 0 ? ' active' : '' }}">
                    <a href="{{ route('permohonan_absen.index') }}" class="sidenav-link"><div>Pengajuan ketidakhadiran</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Absensi.approval'))
                <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen.approval') === 0 ? ' active' : '' }}">
                    <a href="{{ route('permohonan_absen.approval') }}" class="sidenav-link"><div>Approval Pengajuan ketidakhadiran</div></a>
                </li> 
                @endif    
                @if(\UserAuth::hasAccess('Master.mesinabsen'))
                <li class="sidenav-item{{ strpos($routeName, 'absensi_upload') === 0 ? ' active' : '' }}">
                    <a href="{{ route('absensi_upload') }}" class="sidenav-link"><div>Upload Data Absensi</div></a>
                </li>
                @endif            
            </ul>
        </li>
        @endif

        @if(false)
        <!-- \UserAuth::hasAccess('Laporan')) -->
        <li class="sidenav-item{{ strpos($routeName, 'laporan.') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-document"></i><div>Laporan</div></a>
            <ul class="sidenav-menu">
                @if(\UserAuth::hasAccess('Laporan.kehadiranharian'))
                <li class="sidenav-item{{ $routeName == 'laporan.kehadiran_harian' ? ' active' : '' }}">
                    <a href="{{ route('laporan.kehadiran_harian') }}" class="sidenav-link"><div>Kehadiran Harian</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Laporan.rekapkehadiran'))
                <li class="sidenav-item{{ $routeName == 'laporan.rekap_kehadiran' ? ' active' : '' }}">
                    <a href="{{ route('laporan.rekap_kehadiran') }}" class="sidenav-link"><div>Rekap Kehadiran</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Laporan.jejakkehadiran'))
                <li class="sidenav-item{{ $routeName == 'laporan.jejak_kehadiran' ? ' active' : '' }}">
                    <a href="{{ route('laporan.jejak_kehadiran') }}" class="sidenav-link"><div>Jejak Kehadiran</div></a>
                </li>
                @endif                 
            </ul>
        </li>
        @endif

    </ul>
</div>
