import globals from "@/globals";

const state = {
    allConfig: {},
    isConfigSet: null,
    lastReload: null
};

const getters = {
    isConfigSet(state) {
        return state.isConfigSet != null;
    },
    getConfig(state) {
        return state.allConfig;
    },
    apiEndpoint(state) {        
        return globals().Web.getEndpoint(globals().AppConfig.endpoint.api.app) + globals().AppConfig.system.config_endpoint;
    }
};

const mutations = {   
    setConfig(state, allConfig) {
        state.allConfig = allConfig;
        state.isConfigSet = true;        
        // const now = new Date()
        // const expirationDate = new Date(now.getTime() + res.data.expiresIn * 1000)
        state.lastReload = new Date();
    }
};

const actions = {
    reloadConfig({commit,getters},data={}){
        return axios.get(getters.apiEndpoint,{params: data}).then((val)=>{
            commit('setConfig',val.data);            
            return true;
        }).catch((err)=>{
            console.log('Config file error.');
        });
    },
    saveConfig({commit,getters},data={}){
        return axios.post(getters.apiEndpoint,{data: data}).then((val)=>{
            commit('setConfig',val.data);            
            return true;
        }).catch((err)=>{
            console.log('Config file error.');
        });
    }
};

export default {
    state,
    mutations,
    actions,
    getters
};