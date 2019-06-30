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
    isOnAdmin() {
        return 
    }
};

const mutations = {
    addBreadcrumb (state, data) {
      state.example_data = data
    }
};

const actions = {
    updateTemplate({commit}, data) {
        commit('changeData', data)
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