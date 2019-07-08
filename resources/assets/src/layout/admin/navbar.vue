<template>
  <b-navbar
    toggleable="lg"
    :variant="getLayoutNavbarBg()"
    class="layout-navbar align-items-lg-center container-p-x"
  >
    <!-- Brand -->
    <b-navbar-brand :to="{name : 'dashboard'}" class="app-brand demo d-lg-none py-0 mr-4">
      <!-- <span class="app-brand-logo demo bg-primary">
        <svg viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="a" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse"><stop stop-opacity=".25" offset="0"></stop><stop stop-opacity=".1" offset=".3"></stop><stop stop-opacity="0" offset=".9"></stop></linearGradient><linearGradient id="e" x1="76.9" x2="92.64" y1="26.38" y2="31.49" xlink:href="#a"></linearGradient><linearGradient id="d" x1="107.12" x2="122.74" y1="53.41" y2="48.33" xlink:href="#a"></linearGradient></defs><path style="fill: #fff;" transform="translate(-.1)" d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"></path><path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z" fill="url(#a)"></path><path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z" fill="url(#e)"></path><path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z" fill="url(#d)"></path></svg>
      </span> -->
      <span class="app-brand-text demo font-weight-normal ml-2">{{ title }}</span>
    </b-navbar-brand>

    <!-- Sidenav toggle -->
    <b-navbar-nav class="layout-sidenav-toggle d-lg-none align-items-lg-center mr-auto" v-if="sidenavToggle">
      <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)" @click="toggleSidenav">
        <i class="ion ion-md-menu text-large align-middle" />
      </a>
    </b-navbar-nav>

    <!-- Navbar toggle -->
    <b-navbar-toggle target="app-layout-navbar"></b-navbar-toggle>

    <b-collapse is-nav id="app-layout-navbar">

      <b-navbar-nav class="align-items-lg-center">
        <h5 class="font-weight-normal m-0 p-0 navbar-text">{{ tenantName }}</h5>
      </b-navbar-nav>
      
      <b-navbar-nav class="align-items-lg-center ml-auto">
        
        <b-nav-item-dropdown :right="!isRTL" class="demo-navbar-user">
          <template slot="button-content">
            <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
              <div class="avatar-header-block d-block rounded-circle text-center">
                <i class="ion ion-ios-person"></i>
              </div>              
              <span class="px-1 mr-lg-2 ml-2 ml-lg-0">{{ UserAuth.getUser('name') }}</span>
            </span>
          </template>

          <b-dd-item :to="{name: 'myprofile'}"><i class="ion ion-ios-person text-lightest"></i> &nbsp; {{ Trans.get('user.my_profile') }}</b-dd-item>
          <b-dd-item @click="UserAuth.logout()"><i class="ion ion-ios-log-out text-danger"></i> &nbsp; {{ Trans.get('auth.logout') }}</b-dd-item>

          <template v-if="AppConfig.system.mode=='dev'">
            <b-dd-divider />
            <div class="text-center text-muted"><small>Dev Mode Only Action</small></div>
            <b-dd-item @click="Trans.reLoadLang()"><i class="ion ion-md-sync text-lightest"></i> &nbsp; Reload Language</b-dd-item>
          </template>

        </b-nav-item-dropdown>

      </b-navbar-nav>
    </b-collapse>

  </b-navbar>
</template>

<script>
export default {
  computed: {
    title() {
      return this.Web.getAdminTitle();
    },
    tenantName() {
      return this.Web.getTenantName();
    }
  },
  name: "app-layout-navbar",

  props: {
    sidenavToggle: {
      type: Boolean,
      default: true
    }
  },

  methods: {
    toggleSidenav() {
      this.layoutHelpers.toggleCollapsed();
    },

    getLayoutNavbarBg() {
      return this.layoutNavbarBg;
    }
  }
};
</script>
