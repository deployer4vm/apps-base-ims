import Vuex from "vuex";
import VuexPersist from "vuex-persist";
import projectStore from "../../../../app/MainApp/resources/js/store/store";
import templateStore from "./modules/template";
import transStore from "./modules/trans";
import globals from "@/globals";

window.Vue.use(Vuex);

let vuexConfig = {
    modules: {
        trans: transStore,
        template: templateStore,
        ...projectStore
    }
};

const vuexPersist = new VuexPersist({
    //cache semua state kecuali template state
    reducer: (state) => {
        let newState = {'auth':'auth'};
        //registrasikan vuexPersist jika diaktikan
        if (globals().appconfig.system.web_state_persistant) {
            _.forEach(state,(value, index) => {
                if(index != 'template'){
                    newState[index] = value;
                }
            });
        }

        return newState;
    },
    key: globals().appconfig.client.apps_id,
    storage: localStorage
});
vuexConfig.plugins = [vuexPersist.plugin];

export const store = new Vuex.Store(vuexConfig);