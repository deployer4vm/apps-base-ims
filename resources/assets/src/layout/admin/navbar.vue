<template>
    <b-navbar class="layout-navbar navbar navbar-expand align-items-lg-center container-p-x" >
        <!-- Brand saat mode mobile (tidak ada sidebar) -->
        <b-navbar-brand :to="{name : 'home'}" class="app-brand demo d-lg-none py-0 mr-4">
            <!-- <span class="app-brand-logo demo bg-primary">
                <svg viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="a" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse"><stop stop-opacity=".25" offset="0"></stop><stop stop-opacity=".1" offset=".3"></stop><stop stop-opacity="0" offset=".9"></stop></linearGradient><linearGradient id="e" x1="76.9" x2="92.64" y1="26.38" y2="31.49" xlink:href="#a"></linearGradient><linearGradient id="d" x1="107.12" x2="122.74" y1="53.41" y2="48.33" xlink:href="#a"></linearGradient></defs><path style="fill: #fff;" transform="translate(-.1)" d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"></path><path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z" fill="url(#a)"></path><path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z" fill="url(#e)"></path><path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z" fill="url(#d)"></path></svg>
            </span>-->
            <!-- Sidenav toggle -->
            <b-navbar-nav class="layout-sidenav-toggle d-lg-none align-items-lg-center mr-auto" v-if="sidenavToggle">
                <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)" @click="toggleSidenav">
                    <i class="ion ion-md-menu text-large align-middle" />
                </a>
            </b-navbar-nav>
            <!-- Navbar toggle -->
            <span class="app-brand-logo demo bg-primary square">
                <img :src="`${publicUrl}assets/images/logo.png`" />
            </span>
            <div class="nav-item text-big font-weight-light line-height-1 d-none d-sm-block opacity-50 mr-2 ml-3 app-brand-text">
            |</div>

            <span class="app-brand-text demo font-weight-normal ml-2">{{ brandTitle }}</span>
        </b-navbar-brand>


        <b-navbar-toggle target="app-layout-navbar"></b-navbar-toggle>

        <b-navbar-nav class="align-items-center ml-auto">
            <template  v-if="UserAuth.isActive()">
                <template v-if="showNotif">
                    <notif-navbar />
                    <div class="nav-item text-big font-weight-light line-height-1 opacity-75 mr-2 ml-1">|</div>
                </template>

                <b-nav-item-dropdown :right="!isRTL" class="demo-navbar-user">
                    <template slot="button-content">
                        <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                            <div class="d-block ui-w-30 rounded-circle overflow-hidden box-avatar">
                                <img src="/assets/images/avatar.png" alt="">
                            </div>
                            <div class="px-1 mr-lg-2 ml-2 ml-lg-0 d-none d-lg-block text-right line-height-1">
                                <small class="text-muted mb-0">Admin</small>
                                <span class="font-weight-bold d-block">{{ UserAuth.getUser('name') }}</span>
                            </div>
                        </span>
                    </template>

                    <template v-if="AppConfig.system.multilang==1">
                        <b-nav-item-dropdown :right="!isRTL">
                            <template slot="button-content">
                                <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                                    <div class="avatar-header-block d-block rounded-circle text-center">
                                        <i class="ion ion-ios-globe"></i>
                                    </div>
                                    <span class="px-1 mr-lg-2 ml-2 ml-lg-0">{{ Trans.getLocale().toUpperCase() }}</span>
                                </span>
                            </template>
                            <template v-for="(lang,langId) in AppConfig.system.locale">
                                <b-dd-item v-if="Trans.getLocale()==langId" :key="'lang-item-' + langId">
                                    <i class="ion ion-md-radio-button-on text-danger"></i> &nbsp; <span class="text-danger"> {{lang}}</span>
                                </b-dd-item>
                                <b-dd-item v-else @click="Trans.reLoadLang(langId)" :key="'lang-item-' + langId">
                                    <i class="ion ion-md-radio-button-off text-muted"></i> &nbsp; {{lang}}
                                </b-dd-item>
                            </template>
                        </b-nav-item-dropdown>
                        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>
                    </template>

                    <b-dd-item v-if="AppConfig.isModuleEnable('moduser')" :to="{name: 'myprofile'}">
                        <i class="fi fi-rr-man-head"></i>
                        &nbsp; {{ Trans.get('user.my_profile') }}
                    </b-dd-item>

                    <template v-if="AppConfig.isModuleEnable('moduser') && AppConfig.packageLocal.moduser.user_role.multi_role==1 && UserAuth.getAuthRoleCount()>1">
                        <b-dd-divider />
                        <b-dd-item v-for="role in UserAuth.getAuthRoleList()" @click="changeRole(role.role_code)" :key="'header-chose-role-' + role.id">
                            <i :class="{ion:true, 'ion-md-radio-button-on': ActiveRoleCode==role.role_code, 'ion-md-radio-button-off': ActiveRoleCode!=role.role_code, 'text-success':true}"></i> &nbsp; {{role.name}}
                        </b-dd-item>
                        <b-dd-divider />
                    </template>


                    <b-dd-item v-if="AppConfig.isModuleEnable('moduser') && showNotif" :to="{name: 'notification'}">
                        <i class="fi fi-rr-bell"></i>
                        &nbsp; {{ Trans.get('notif.notification_title') }}
                    </b-dd-item>

                    <b-dd-item v-if="AppConfig.isModuleEnable('moduser')" @click="UserAuth.logout()">
                        <i class="fi fi-rr-sign-out-alt text-danger"></i>
                        &nbsp; {{ Trans.get('auth.logout') }}
                    </b-dd-item>

                    <template v-if="AppConfig.system.mode=='dev'">
                        <b-dd-divider />
                        <div class="text-center text-muted">
                            <small>Dev Mode Only Action</small>
                        </div>
                        <b-dd-item @click="Trans.reLoadLang()">
                            <i class="fi fi-rr-refresh"></i> &nbsp; Reload Language
                        </b-dd-item>
                    </template>
                </b-nav-item-dropdown>
            </template>
        </b-navbar-nav>
    </b-navbar>
</template>

<script>
export default {
    data(){
        return {
            ActiveRoleCode: ''
        };
    },
    computed: {
        brandTitle() {
            return this.Web.getAdminTitle();
        },
        navbarTitle() {
            return this.Web.getNavbarTitle()?this.Web.getNavbarTitle():this.Web.getTenantName();
        },
        showNotif() {
            return this.AppConfig.isModuleEnable('moduser') && this.AppConfig.packageLocal.moduser.notification.enable==1 && this.AppConfig.packageLocal.moduser.notification.show==1;
        },
        logoPath() {
            return this.publicUrl + (this.AppConfig.system.template.logo?this.AppConfig.system.template.logo:'assets/images/logo.png');
        }
    },
    name: "app-layout-navbar",
    props: {
        sidenavToggle: {
            type: Boolean,
            default: true
        }
    },
    created(){
        if(this.UserAuth.isActive())
            this.ActiveRoleCode = this.UserAuth.getAuthRole().role_code;
    },
    methods: {
        changeRole(roleCode) {
            var that = this;
            if(this.ActiveRoleCode!=roleCode){
                this.UserAuth.changeRole(roleCode).then(res=>{
                    that.Web.showAlert({type: 'info', text: 'Role changed to <b>' + this.UserAuth.getAuthRole().name + '</b>'});
                    that.ActiveRoleCode = roleCode;
                });
            }
        },
        toggleSidenav() {
            this.layoutHelpers.toggleCollapsed();
        },
        getLayoutNavbarBg() {
            return this.layoutNavbarBg;
        }
    }
};
</script>
