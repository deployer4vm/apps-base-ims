import Vuex from "vuex";
import VuexPersist from "vuex-persist";
import projectStore from "../../../../app/MainApp/resources/js/store/store";
import templateStore from "./modules/template";
import transStore from "./modules/trans";
import tenantStore from "./modules/tenant";
import globals from "@/globals";

window.Vue.use(Vuex);

let vuexConfig = {
    modules: {
        tenant: tenantStore,
        trans: transStore,
        template: templateStore,
        ...projectStore
    }
};

const vuexPersist = new VuexPersist({
    //cache semua state kecuali template state
    reducer: (state) => {
        let newState = {'auth':state.auth,'tenant':state.tenant};
        //registrasikan vuexPersist jika diaktikan
        if (globals().AppConfig.system.web_state_persistant) {
            _.forEach(state,(value, index) => {
                if(index != 'template'){
                    newState[index] = value;
                }
            });
        }

        return newState;
    },
    key: globals().AppConfig.client.apps_id,
    storage: localStorage
});
vuexConfig.plugins = [vuexPersist.plugin];

export const store = new Vuex.Store(vuexConfig);