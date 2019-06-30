import Vue from "vue";
import Router from "vue-router";
import Meta from "vue-meta";

import globals from "@/globals";

//load routes level project
import projectRoutes from "../../../../app/MainApp/resources/js/router/index";
//load all routes web modules general
import modulesRoutes from "../../../../app/MainApp/resources/js/router/modules";
//load all routes web modules admin
// import modulesAdminRoutes from "../../../../app/MainApp/resources/js/router/modulesAdmin";

Vue.use(Router);
Vue.use(Meta);

let Routes = [...projectRoutes];
if(window.appconfig.system.web_admin.autoload_router){
    Routes.push({    
        path: window.appconfig.client.endpoint[window.appconfig.system.mode]['admin'],
        component: () => import('@/layout/' + window.appconfig.system.web_admin.layout),
        children: import("../../../../app/MainApp/resources/js/router/modulesAdmin")
    });
}
Routes.concat(modulesRoutes);

const router = new Router({
    base: "/",
    mode: "history",
    routes: Routes
});

router.afterEach(() => {
    // On small screens collapse sidenav
    if (
        window.layoutHelpers &&
        window.layoutHelpers.isSmallScreen() &&
        !window.layoutHelpers.isCollapsed()
    ) {
        setTimeout(() => window.layoutHelpers.setCollapsed(true, true), 10);
    }

    // Scroll to top of the page
    globals().scrollTop(0, 0);
});

router.beforeEach((to, from, next) => {
    // Set loading state
    document.body.classList.add("app-loading");

    // Add tiny timeout to finish page transition
    setTimeout(() => next(), 10);
});

export default router;
