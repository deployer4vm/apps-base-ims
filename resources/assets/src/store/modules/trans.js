import globals from "@/globals";

const state = {
    allLang: {},
    isLangSet: null,
    lastReload: null,
    lang: null
};

const getters = {
    isLangSet(state) {
        return state.isLangSet != null;
    },
    getLang(state) {
        return state.allLang;
    },
    getLocale(state) {
        return state.lang;
    }
};

const mutations = {   
    setLang(state, allLang) {
        state.allLang = allLang;
        state.isLangSet = true;
        // const now = new Date()
        // const expirationDate = new Date(now.getTime() + res.data.expiresIn * 1000)
        state.lastReload = new Date();
    },
    setLocale(state, lang) {
        state.lang = lang;
    }
};

const actions = {
    reloadLang({commit,state},newLang=null){
        var params = {lang: null};
        var apiPath = 
            globals().AppConfig.endpoint.api.app + 
            globals().AppConfig.system.lang_endpoint;

        params.lang = state.lang;
        if(newLang!=null)
            params.lang = newLang;
        if(state.lang == null && newLang == null)
            params.lang = globals().AppConfig.system.fallback_locale;
        if(state.lang != params.lang)
            commit('setLocale',params.lang);        

        return axios.get(apiPath,{params: params}).then((val)=>{
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