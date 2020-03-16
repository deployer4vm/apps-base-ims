<template>
    <div class="layout-wrapper layout-2">
        <div class="layout-inner" v-if="showComponent">
            <app-layout-navbar />

            <div class="layout-container">
                <app-layout-sidenav />

                <div class="layout-content">
                    <div
                        :class="{
                            'router-transitions': true,
                            'container-fluid': true,
                            'flex-grow-1': true,
                            'p-3': bodyWithPadding,
                            'p-0': !bodyWithPadding,
                            'pt-0': !bodyWithPadding,
                            'pb-0': !bodyWithPadding
                        }"
                    >
                        <router-view />
                    </div>

                    <app-layout-footer v-if="showFooter" />
                </div>
            </div>
        </div>
        <div class="layout-inner" v-else>
            <div
                class="text-mutted h- row align-items-center"
                style="width: 100%;"
            >
                <div class="col">
                    <div class="sk-cube-grid sk-primary">
                        <div class="sk-cube sk-cube1"></div>
                        <div class="sk-cube sk-cube2"></div>
                        <div class="sk-cube sk-cube3"></div>
                        <div class="sk-cube sk-cube4"></div>
                        <div class="sk-cube sk-cube5"></div>
                        <div class="sk-cube sk-cube6"></div>
                        <div class="sk-cube sk-cube7"></div>
                        <div class="sk-cube sk-cube8"></div>
                        <div class="sk-cube sk-cube9"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layout-overlay" @click="closeSidenav"></div>
    </div>
</template>

<style>
.avatar-header-block {
    height: 22px;
    width: 22px;
}
.avatar-header-block i.ion {
    padding-top: 5px;
}
.sidenav-app-brand {
    height: 58px;
}
.default-style .sidenav .app-brand.sidenav-app-brand {
    height: 58px;
}
.sidenav-button-onhover {
    font-size: 180%;
    padding: 0 25px;
    display: none;
}
.layout-sidenav-hover .sidenav-button-onhover,
.layout-expanded .sidenav-button-onhover {
    display: none !important;
}
.layout-collapsed .sidenav-button-onhover {
    display: inline;
}

/* *****************************************************************************
 * Navbar
 */

.demo-navbar-messages .dropdown-toggle,
.demo-navbar-notifications .dropdown-toggle,
.demo-navbar-user .dropdown-toggle,
.demo-navbar-messages.b-nav-dropdown .nav-link,
.demo-navbar-notifications.b-nav-dropdown .nav-link,
.demo-navbar-user.b-nav-dropdown .nav-link {
    white-space: nowrap;
}

.demo-navbar-messages .dropdown-menu,
.demo-navbar-notifications .dropdown-menu {
    overflow: hidden;
    padding: 0;
}

@media (min-width: 992px) {
    .demo-navbar-messages .dropdown-menu,
    .demo-navbar-notifications .dropdown-menu {
        margin-top: 0.5rem;
        width: 22rem;
    }

    .demo-navbar-user .dropdown-menu {
        margin-top: 0.25rem;
    }
}
</style>

<script>
import navbar from "./navbar";
import sidenav from "./sidenav";
import footer from "./footer";

export default {
    name: "app-admin-1",
    components: {
        "app-layout-navbar": navbar,
        "app-layout-sidenav": sidenav,
        "app-layout-footer": footer
    },

    mounted() {
        this.layoutHelpers.init();
        this.layoutHelpers.update();
        this.layoutHelpers.setAutoUpdate(true);
    },

    beforeDestroy() {
        this.layoutHelpers.destroy();
    },
    computed: {
        bodyWithPadding() {
            return this.$store.getters.isBodyWithPadding;
        },
        showFooter() {
            return this.$store.getters.isFooterShowed;
        },
        showComponent() {
            return this.AppConfig.system.has_acl == 0 || this.UserAuth.isLogin()
                ? true
                : false;
        }
    },
    methods: {
        closeSidenav() {
            this.layoutHelpers.setCollapsed(true);
        }
    }
};
</script>
