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
    /*
    Route & Endpoint
    =======================================================================
    */
    get curAppEndpoint() {
        if(this.isAdminEndpoint()){
            return this.endpoint.admin.app;
        }
        return this.endpoint.frontend.app;
    },
    get adminEndpoint() {
        return this.endpoint.admin.app;
    },
    getModuleEndpoint(packageNamespace,app = 'admin') {
        return this.endpoint[app][packageNamespace];
    },
    /*
    cek apakah url sekarang adalah path yang diinputkan
    */
    isOnEndpoint(path = null) {
        return this.router.currentRoute.path.indexOf(path) === 0;
    },
    isAuthEdnpoint(path = null,app='admin') {
        if (path == null) {
            path = this.router.currentRoute.path;
        }
        return path.indexOf(this.endpoint[app]['auth']) === 0;
    },
    //cek apakah halaman yang diakses sekarang admin area
    isAdminEndpoint(path = null,app=null) {
        if(this.endpoint.admin.app=='')return true;
        if (path == null) {
            path = this.router.currentRoute.path;
        }
        if(app==null){
            _.forEach(this.endpoint,(v,k)=>{

            });
        }
        return path.indexOf(this.endpoint.admin.app) === 0;
    },
    /*
    template
    =======================================================================
    */
    //initialize template store vuex
    initTemplateState() {
        return this.store.dispatch("initTemplateState");
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
    styleType :
        hover
        standar
    */
    showAlert(params={
        title: 'Alert',
        text: 'Shome Warning',
        type: 'info',
        styleType: 'hover', 
        position: 'top-center'
    }) {

        if(!params.type)params.type = 'info';
        if(!params.title)params.title = 'Alert';
        if(!params.text)params.text = 'Shome Warning';
        if(!params.styleType)params.styleType = 'hover';
        if(!params.position)params.position = 'top-center';

        if(this._showAlert_type[params.type] == undefined)
            params.type = 'info';
        if(this._showAlert_position[params.position] == undefined)
            params.position = 'top-center';

        this.notify({
            group: this._showAlert_position[params.position],
            type: this._showAlert_type[params.type],
            title: params.title,
            text: params.text
        });
    },
    _showAlert_position: {
        'top-left': 'notifications-top-left',
        'top-center': 'notifications-top-center',
        'default': 'notifications-default',
        'bottom-left': 'notifications-bottom-left',
        'bottom-center': 'notifications-bottom-center',
        'bottom-right': 'notifications-bottom-right'
    },
    _showAlert_type: {
        warning: 'bg-warning text-body',
        success: 'bg-success text-white',
        info: 'bg-info text-white',
        danger: 'bg-danger text-white',
        secondary: 'bg-secondary text-white',
        dark: 'bg-dark text-white'
    },
    //tampilkan alert di halaman selanjutnya
    showNextAlert() {

    }
};
