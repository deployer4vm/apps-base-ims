const state = {
    frontend: {
        title: window.appconfig.system.template.frontend.title,
        breadcrumb:[
            
        ]
    },
    admin: {
        title: window.appconfig.system.template.admin.title,
        header: {
            appsbar: {

            },
            tabs: [
                {
                    closable: 0,
                    title: "Home",
                    route: "/dashboard"
                }
            ]
        },
        breadcrumb:[
            {
                title: "Home",
                link: "/"
            }            
        ],
        sidebar: [
            {
                title: "",
                children: [
                    {
                        
                    }
                ]
            }
        ],
        footer: {
            text: window.appconfig.system.template.admin.footer.text,
            menu: window.appconfig.system.template.admin.footer.menu
        }
    }    
};

const getters = {
    isOnAdmin(state) {
        return ;
    },
    getAdminTitle(state) {
        return state.admin.title;
    }
};

const mutations = {
    addBreadcrumb (state, data) {
      state.example_data = data;
    },
    setAdminTitle (state, newTitle) {
      state.admin.title = newTitle;
    }
};

const actions = {
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