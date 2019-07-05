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
    frontend: {
        title: globals().appconfig.system.template.frontend.title,
        breadcrumb:[
            
        ]
    },
    admin: {
        title: globals().appconfig.system.template.admin.title,
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
            show: globals().appconfig.system.template.admin.footer.show,
            text: globals().appconfig.system.template.admin.footer.text,
            menu: globals().appconfig.system.template.admin.footer.menu
        }
    }    
};

const getters = {
    isOnAdmin(state) {
        return ;
    },
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
    //---------------navbar (header-------------------
    setAdminTitle (state, newTitle) {
        state.admin.title = newTitle;
    },    
    //---------------sidenav-------------------
    setSidenavMenu (state) {
        _.forEach(globals().appconfig.packageLocal, (value, index) => {
            if(value.access &&  value.enable){
                state.admin.sidenav[index] = value.access;
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