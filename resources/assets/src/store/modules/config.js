import globals from "@/globals";
var apiConfig = globals().AppConfig.endpoint.api.app + globals().AppConfig.system.config_endpoint;
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
    reloadConfig({commit},data={}){
        return axios.get(apiConfig,{params: data}).then((val)=>{
            commit('setConfig',val.data);            
            return true;
        }).catch((err)=>{
            console.log('Config file error.');
        });
    },
    saveConfig({commit},data={}){
        return axios.post(apiConfig,{data: data}).then((val)=>{
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