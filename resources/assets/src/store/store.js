import Vuex from "vuex";
import VuexPersist from "vuex-persist";
import projectStore from "../../../../app/MainApp/resources/js/store/store";
import templateStore from "./modules/template";
import authStore from "./modules/auth";

window.Vue.use(Vuex);

let vuexConfig = {
    modules: {
        template: templateStore,
        auth: authStore,
        ...projectStore
    }
};

//registrasikan vuexPersist jika diaktikan
if (window.appconfig.system.web_state_persistant) {
    const vuexPersist = new VuexPersist({
        //cache semua state kecuali template state
        reducer: (state) => {
            let newState = {};
            _.forEach(state,(value, index) => {
                if(index != 'template'){
                    newState[index] = value;
                }
            });
            return newState;
        },
        key: window.appconfig.client.apps_id,
        storage: localStorage
    });
    vuexConfig.plugins = [vuexPersist.plugin];
}

export const store = new Vuex.Store(vuexConfig);