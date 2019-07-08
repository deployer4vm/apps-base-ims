import globals from "@/globals";

const state = {
    nextAlert: { // route yg ditampilkan dihalaman selanjutnya
        show: false,
        alertStyleType: "hover",
        isInstant: 1, //1/0
        data: {
            type: "",
            title: "",
            content: ""
        }
    },   
    alertData:[],
    alertModal:{
        title: "Warning",
        text: "",
        onOk: null,
        onShow: null,
        onClose: null,
        modalButtonCancel: 'Close',
        modalButtonOk: 'Ok'
    },
    frontend: {
        title: globals().AppConfig.system.template.frontend.title,
        breadcrumb:[
            
        ]
    },
    admin: {
        title: globals().AppConfig.system.template.admin.title,
        navbar: {
            appsbar: {

            },
            tabs: [
                {
                    closable: 0,
                    title: "Home",
                    route: "/dashboard"
                }
            ],
            menu: {}
        },
        breadcrumb:[
            {
                title: "Home",
                link: "/"
            }            
        ],
        sidenav: {},
        footer: {
            show: globals().AppConfig.system.template.admin.footer.show,
            text: globals().AppConfig.system.template.admin.footer.text,
            menu: globals().AppConfig.system.template.admin.footer.menu
        }
    }    
};

const getters = {
    //---------------navbar (header-------------------
    getAdminTitle(state) {
        return state.admin.title;
    },
    getTabs(state) {
        return state.admin.navbar.tabs;
    },
    //---------------sidenav-------------------
    getSidenavMenu(state) {
        return state.admin.sidenav;
    },
    
    //---------------body-------------------
    getBreadcrumb(state) {
        return state.admin.sidenav;
    },
        
    //---------------footer-------------------
    isFooterShowed(state) {
        return state.admin.footer.show;
    },
    getFooterText(state) {
        return state.admin.footer.text;
    },
    getFooterMenu(state) {
        return state.admin.footer.menu;
    }
};

const mutations = {    
    addAlert(state, alert){
        if(state.alertData.length >=3)delete state.alertData[0];
        state.alertData.push(alert);
    },
    deleteAlert(state, k){
        delete state.alertData[k];
    },
    setModal(state, v){
        state.alertModal.title = v.title;
        state.alertModal.text = v.text;
        state.alertModal.onShow = v.onShow;
        state.alertModal.onOk = v.onOk;
        state.alertModal.onClose = v.onClose;
        state.alertModal.modalButtonCancel = v.modalButtonCancel?v.modalButtonCancel:globals().Trans.get('alert.modal_cancel_caption');
        state.alertModal.modalButtonOk = v.modalButtonOk?v.modalButtonOk:globals().Trans.get('alert.modal_ok_caption');
    },
    //---------------navbar (header-------------------
    setAdminTitle (state, newTitle) {
        state.admin.title = newTitle;
    },    
    //---------------sidenav-------------------
    setSidenavMenu (state) {
        _.forEach(globals().AppConfig.packageLocal, (value, index) => {
            if(value.access.has_acl == 0 ||(value.access &&  value.enable &&  value.access.has_access)){
                state.admin.sidenav[index] =value.access;
            }
        });
    },
    //---------------body-------------------
    addBreadcrumb (state, data) {
        state.example_data = data;
    },
};

const actions = {
    //initialize yang perlu diinitialize
    //action ini dieksekusi saat vue instace utama created
    initTemplateState({commit}){
        commit('setSidenavMenu');
    },
    setAdminTitle({commit}, newTitle) {
        commit('setAdminTitle', newTitle);
    },
    updateTemplate({commit}, data) {
        commit('changeData', data);
    },    
    addBreadcrumb({commit}, data){

    }
};

export default {
    state,
    mutations,
    actions,
    getters
};