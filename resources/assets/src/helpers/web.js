/*
Template helper
diakses via this.Web di vue instance
*/
export default {
    mode: "dev",
    store: null,
    router: null,
    notify: null, //vue-notification
    endpoint: null, //local full endpoint dari AppConfig.endpoint
    bvModal: null,
    tenantList: null,
    multitenantConfig: null,
    langDefault: {
        title: "Alert",
        text: "Someting went wrong!"
    },
    /*
    Route & Endpoint
    =======================================================================
    */
   //get path yg sedang diakses sekarang
    get curEndpoint() {
        return this.router.currentRoute.path;
    },
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
    cek apakah url yang sedang diakses sekarang adalah bagian (diawali dengan) path (parameter)
    */
    isOnEndpoint(path = null) {
        //get url yg sedang diakses sekarang
        let endpoint = path.replace(':group_app',this.router.currentRoute.params.group_app);
        //ceka apakah diawali dengan 'path'
        return this.router.currentRoute.path.indexOf(endpoint) === 0;
    },
    //cek apakah 'path' adalah url auth endpoin di aplikasi 'app'
    isAuthEdnpoint(path = null, app = "admin") {
        if (path == null) {
            path = this.router.currentRoute.path;
        }
        //get url/path auth
        let atuhEndpoint = this.endpoint[app]["auth"].replace(':group_app',this.router.currentRoute.params.group_app);
        //cek apakah parameter auth yg diinputkan berarawalan path auth
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
        // console.log('go to default tenant : ', this.router.resolve(this.multitenantConfig.default_route).href);
        this.router.push(this.multitenantConfig.default_route);
    },    
    goToCurrentTenant() {
        this.router.push({name: "dashboard",params:{group_app: this.getTenantGroupApp()}});
    },   
    goToTenant(groupApp) {
        this.router.push({name: "dashboard",params:{group_app: groupApp}});
    },   
    /*
    tenant
    =======================================================================
    */
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
    //force reload tenant data from server
    reLoadTenant(groupApp) {
        this.store.dispatch('reloadTenant',groupApp).then((val)=>{
            this.tenantList = this.store.getters.getTenantList;
        });       
    },
    /**
     * get defautl tenant route, format :
     *  {"name":"homeadmin","params":{"group_app":"admin"}}
     */
    getDefaultTenantRoute() {
        return this.multitenantConfig.default_route;
    },
    //get active tenant record
    get getTenant() {
        return this.store.getters.getTenant;
    },
    //get active tenant name
    getTenantGoup() {
        return this.store.getters.getTenantGroup;
    },
    //get active tenant id
    getTenantId() {
        return this.store.getters.getTenantId;
    },
    //get active tenant name
    getTenantName() {
        return this.store.getters.getTenantName;
    },
    //get active tenant group_app
    getTenantGroupApp() {
        return this.store.getters.getTenantGroupApp;
    },
    /*
    template
    =======================================================================
    */
    //initialize template store vuex
    initTemplateState () {
        return this.store.dispatch("initTemplateState");
    },    
    setLoadingPage(showLoading=false,message='') {
        if(this.store!=null) {
            this.store.commit("setMessagePageLoading",message);
            this.store.commit("setPageLoading",showLoading);
        }
    },
    //---------------navbar (header)-------------------
    //admin title digunakan di meta title dan brand/apps bar
    getAdminTitle() {
        return this.store.getters.getAdminTitle;
    },
    // admin title tidak boleh diubah
    // appendAdminTitle(title)
    // {
    //     this.store.commit("setAdminTitle", this.store.getters.getAdminTitle + ' - ' + title);
    // },
    // setAdminTitle(newTitle) 
    // {
    //     this.store.commit("setAdminTitle", newTitle);
    // },
    //-----
    //title di navbar atas
    getNavbarTitle() {
        return this.store.getters.getNavbarTitle;
    },
    appendNavbarTitle(title)
    {
        this.store.commit("setNavbarTitle", this.store.getters.getNavbarTitle + ' \\ ' + title);
    },
    setNavbarTitle(newTitle) 
    {
        this.store.commit("setNavbarTitle", newTitle);
    },
    //---------------sidenav-------------------
    getSidenavMenu() 
    {
        return this.store.getters.getSidenavMenu;
    },
    //---------------body-------------------
    addBreadcrumb(item, isAdmin = true) {
        this.store.dispatch("addBreadcrumb", item);
    },
    setBodyWithPadding(isWithPadding) {
        this.store.commit("setBodyWithPadding", isWithPadding);
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
        if (!params.title) params.title = this.langDefault.title;
        if (!params.text) params.text = this.langDefault.text;
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
            let duration = 3000;
            let newDur = parseInt(params.text.length / 20) * 1000;
            if(newDur > duration)duration = newDur;
            this.notify({
                group: this._showAlert_position[params.position],
                type: this._showAlert_type[params.type],
                title: params.title,
                text: params.text,
                duration: duration
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
        warning: "bg-warning warn text-body",
        success: "bg-success success text-white",
        info: "bg-info text-white",
        danger: "bg-danger error text-white",
        primary: "bg-primary text-white",
        secondary: "bg-secondary text-white",
        dark: "bg-dark text-white"
    },
    //tampilkan alert di halaman selanjutnya
    showNextAlert() {}
};
