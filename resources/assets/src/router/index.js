import Vue from "vue";
import Router from "vue-router";
// import NProgress from 'node_modules/nprogress';
import Meta from "vue-meta";
// import authAxios from "axios";
import BlankRouterContainer from '@/layout/BlankRouterContainer';
import globals from "@/globals";

//load routes level project
import projectRoutes from "../../../../app/MainApp/resources/js/router/index";
//load all routes web modules general
import modulesRoutes from "../../../../app/MainApp/resources/js/router/modules";
//load all routes web modules admin
import modulesAdminRoutes from "../../../../app/MainApp/resources/js/router/modulesAdmin";

Vue.use(Router);
Vue.use(Meta);

let tmpRoutes = [...projectRoutes];
// if (globals().AppConfig.system.web_admin.autoload_router.frontend) {
    tmpRoutes.push({
        name: "homeadmin",
        path: globals().AppConfig.endpoint.admin.app,
        component: BlankRouterContainer,
        children: modulesAdminRoutes
    });
// }
tmpRoutes.concat(modulesRoutes);

const router = new Router({
    base: "/",
    mode: "history",
    routes: tmpRoutes
});

router.afterEach((to, from) => {
    /*
    detek dan proteksi halaman admin dengan auth jika diaktifkan di config
    */
    if(
        globals().AppConfig.system.has_auth &&
        globals().AppConfig.system.web_admin.protected_by_auth &&
        globals().Web.isAdminEndpoint()
    ){
        //jika tidak login dan mengakses halaman selain auth maka redire
        if(!globals().UserAuth.isLogin() && !globals().Web.isAuthEdnpoint() ){
            globals().UserAuth.goToLogin();
        //jika sudah login tapi mengakses halaman auth maka redirect
        }else if( globals().UserAuth.isLogin() && globals().Web.isAuthEdnpoint() ){
            globals().UserAuth.goToDashboard();
        }      
        
        //jika berpindah tenant maka logout kan
        if(
            globals().UserAuth.isLogin() 
            && globals().AppConfig.system.web_admin.multitenant.active 
            && to.params.group_app != globals().Web.getTenantGroupApp()
        ){
            globals().UserAuth.logout();
        }  
    }
    
    if(globals().LocalApi.defaults.headers.common["App-Group"] != to.params.group_app)
        globals().LocalApi.defaults.headers.common["App-Group"] = to.params.group_app;
        
    if(globals().AppConfig.system.web_admin.multitenant.active && to.params.group_app != globals().Web.getTenantGroupApp()){
        globals().Web.loadTenant(to.params.group_app).then((val)=>{
            //jika tenant tidak ditemukan
            if(!val){
                globals().Web.goToDefaultTenant();
            }
        }); 
    }
    

    // Remove initial splash screen
    const splashScreen = document.querySelector(".app-splash-screen");
    if (splashScreen) {
        splashScreen.style.opacity = 0;
        setTimeout(
            () =>
                splashScreen &&
                splashScreen.parentNode.removeChild(splashScreen),
            300
        );
    }

    // On small screens collapse sidenav
    if (
        globals().layoutHelpers &&
        globals().layoutHelpers.isSmallScreen() &&
        !globals().layoutHelpers.isCollapsed()
    ) {
        setTimeout(() => globals().layoutHelpers.setCollapsed(true, true), 10);
    }

    // Scroll to top of the page
    globals().scrollTop(0, 0);
    globals().Web.setLoadingPage(false);
    // NProgress.done();
});

router.beforeEach((to, from, next) => {
    if (to.name) {
        globals().Web.setLoadingPage(true);
        // NProgress.start();
    }
    // Set loading state
    document.body.classList.add("app-loading");

    // Add tiny timeout to finish page transition
    setTimeout(() => next(), 10);
});

export default router;
