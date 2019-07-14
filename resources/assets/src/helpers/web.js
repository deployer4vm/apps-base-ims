/*
Template helper
diakses via this.Web di vue instance
*/
export default {
    mode: "dev",
    store: null,
    router: null,
    notify: null, //vue-notification
    endpoint: null, //local full endpoint
    bvModal: null,
    tenantList: null,
    multitenantConfig: null,
    /*
    Route & Endpoint
    =======================================================================
    */
    get curAppEndpoint() {
        if (this.isAdminEndpoint()) {
            return this.endpoint.admin.app;
        }
        return this.endpoint.frontend.app;
    },
    get adminEndpoint() {
        return this.endpoint.admin.app;
    },
    getModuleEndpoint(packageNamespace, app = "admin") {
        return this.endpoint[app][packageNamespace];
    },
    /*
    cek apakah url sekarang adalah path yang diinputkan
    */
    isOnEndpoint(path = null) {
        let endpoint = path.replace(':group_app',this.router.currentRoute.params.group_app);
        return this.router.currentRoute.path.indexOf(endpoint) === 0;
    },
    isAuthEdnpoint(path = null, app = "admin") {
        if (path == null) {
            path = this.router.currentRoute.path;
        }
        let atuhEndpoint = this.endpoint[app]["auth"].replace(':group_app',this.router.currentRoute.params.group_app);
        return path.indexOf(atuhEndpoint) === 0;
    },
    //cek apakah halaman yang diakses sekarang admin area
    isAdminEndpoint(path = null, app = null) {
        if (this.endpoint.admin.app == "") return true;
        if (path == null) {
            path = this.router.currentRoute.path;
        }
        let adminEndpoint = this.endpoint.admin.app.replace(':group_app',this.router.currentRoute.params.group_app);
        return path.indexOf(adminEndpoint) === 0;
    },
    //----------go to------    
    goToDefaultTenant() {
        this.router.push(this.multitenantConfig.default_route);
    },   
    /*
    template
    =======================================================================
    */
    //initialize template store vuex
    initTemplateState () {
        return this.store.dispatch("initTemplateState");
    },    
    //------tenant--------------------------
    loadTenant (groupApp) {
        if(!this.store.getters.isTenantLoaded || groupApp != this.store.getters.getTenantGroupApp){
            return this.store.dispatch('reloadTenant',groupApp).then((val)=>{
                if(!val){
                    return false;
                }
                this.tenantList = this.store.getters.getTenantList;
                return true;
            });
        }else{
            this.tenantList = this.store.getters.getTenantList;
            return new Promise((resolve,reject)=>{
                resolve(true);
            });
        }
    },
    //force reload languange from server
    reLoadTenant(groupApp) {
        this.store.dispatch('reloadTenant',groupApp).then((val)=>{
            this.tenantList = this.store.getters.getTenantList;
        });       
    },
    //get active tenant name
    getTenantGoup() {
        return this.store.getters.getTenantGroup;
    },
    //get active tenant name
    getTenantName() {
        return this.store.getters.getTenantName;
    },
    //get active tenant name
    getTenantGroupApp() {
        return this.store.getters.getTenantGroupApp;
    },
    //---------------navbar (header)-------------------
    getAdminTitle() {
        return this.store.getters.getAdminTitle;
    },
    setAdminTitle(newTitle) {
        this.store.dispatch("setAdminTitle", newTitle);
    },
    //---------------sidenav-------------------
    getSidenavMenu() {
        return this.store.getters.getSidenavMenu;
    },
    //---------------body-------------------
    addBreadcrumb(item, isAdmin = true) {
        this.$store.dispatch("addBreadcrumb", item);
    },
    /*
    tampilkan alert instan
    params :
        type
        styleType : berisi 'alert', 'notif' atau 'modal'
        title
        text
        position

        onShow
        onClose
        onOk

        modalButtonCancel
        modalButtonOk
    */
    showAlert(params) {
        if (!params.type) params.type = "info";
        if (!params.title) params.title = "Alert";
        if (!params.text) params.text = "Shome Warning";
        if (!params.styleType) params.styleType = "hover";
        if (!params.position) params.position = "top-center";
        
        if (this._showAlert_type[params.type] == undefined)
            params.type = "info";
        if (this._showAlert_position[params.position] == undefined)
            params.position = "top-center";

        if (params.styleType == "alert") {
            this.store.commit("addAlert", {
                text: params.text,
                type: params.type
            });
        } else if (params.styleType == "modal") {
            if (!params.onShow) params.onShow = null;
            if (!params.onCancel) params.onCancel = null;
            if (!params.onOk) params.onOk = null;
            if (!params.modalButtonCancel) params.modalButtonCancel = null;
            if (!params.modalButtonOk) params.modalButtonOk = null;
            this.store.commit("setModal", {
                title: params.title,
                text: params.text,
                onShow: params.onShow,
                onClose: params.onClose,
                onOk: params.onOk,
                modalButtonCancel: params.modalButtonCancel,
                modalButtonOk: params.modalButtonOk
            });
            this.bvModal.show("alert-modals");
        } else {
            this.notify({
                group: this._showAlert_position[params.position],
                type: this._showAlert_type[params.type],
                title: params.title,
                text: params.text
            });
        }
    },
    _showAlert_position: {
        "top-left": "notifications-top-left",
        "top-center": "notifications-top-center",
        default: "notifications-default",
        "bottom-left": "notifications-bottom-left",
        "bottom-center": "notifications-bottom-center",
        "bottom-right": "notifications-bottom-right"
    },
    _showAlert_type: {
        warning: "bg-warning text-body",
        success: "bg-success text-white",
        info: "bg-info text-white",
        danger: "bg-danger text-white",
        primary: "bg-primary text-white",
        secondary: "bg-secondary text-white",
        dark: "bg-dark text-white"
    },
    //tampilkan alert di halaman selanjutnya
    showNextAlert() {}
};
