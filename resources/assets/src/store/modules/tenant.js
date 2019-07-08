import globals from "@/globals";

const state = {
    isTenantLoaded: 0,
    // tenantList: null,
    activeTenant: {
        id: 0,
        name: '',
        group_app: '',
    },
    activeGroup: null
};

const getters = {
    isTenantLoaded(state) {
        return state.isTenantLoaded;
    },
    getTenantList(state) {
        return state.tenantList;
    },
    getTenantGroup(state) {
        return state.activeGroup;
    },
    getTenant(state) {
        return state.activeTenant;
    },
    getTenantName(state) {
        return state.activeTenant.name;
    },
    getTenantGroupApp(state) {
        return state.activeTenant.group_app;
    },
};

const mutations = {   
    setTenant(state, data) {
        // state.tenantList = data.tenant_list?data.tenant_list:null;
        state.activeTenant = data.active_tenant?data.active_tenant:{id: 0,name: '',group_app: '',};
        state.activeGroup = data.active_tenant_group?data.active_tenant_group:null;
    },
    setActiveTenant(state) {
        state.isTenantLoaded = 1;
    }
};

const actions = {
    reloadTenant({commit},groupApp){
        return axios.get(globals().AppConfig.endpoint.domain + globals().AppConfig.system.web_admin.multitenant.api_endpoint,{
            params: {
                group_app: groupApp
            }
        }).then((val)=>{
            commit('setTenant',val.data);
            if(val.data.active_tenant){
                commit('setActiveTenant');
                return true;
            }
            return false;
        }).catch((err)=>{
            console.log('Tenant config error : ',err);
        });
    }
};

export default {
    state,
    mutations,
    actions,
    getters
};