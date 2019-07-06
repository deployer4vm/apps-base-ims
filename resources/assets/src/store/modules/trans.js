import globals from "@/globals";

const state = {
    allLang: {},
    isLangSet: null
};

const getters = {
    isLangSet(state) {
        return state.isLangSet != null;
    },
    getLang(state) {
        return state.allLang;
    }
};

const mutations = {   
    setLang(state, allLang) {
        state.allLang = allLang;
        state.isLangSet = true;
    }
};

const actions = {
    reloadLang({commit}){
        return axios.get(globals().AppConfig.endpoint.domain + globals().AppConfig.system.lang_endpoint).then((val)=>{
            commit('setLang',val.data);
            return true;
        }).catch((err)=>{
            console.log('Language file error.');
        });
    }
};

export default {
    state,
    mutations,
    actions,
    getters
};