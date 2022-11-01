import globals from "@/globals";
import storeConfig from "../../../../app/MainApp/resources/js/store/storeConfig";

const state = {
    defData: {            
        module:'',
        apiEndpoint : '',
        listData: {
            data: [],
            count: 0,
            limit: 10,
            offset: 0,
            currentPage: 1,
            pageCount: 1,
        },
        oneData: {},
    },
    // all resource data
    data:{}
};

_.forEach(storeConfig,(v,k)=>{
    state.data[k] = v;
    state.data[k].listData = state.defData.listData;
    state.data[k].oneData = state.defData.oneData;
});

const getters = {
    
};

const mutations = {
    setListData( state, params ) {
        state.data[params.module].listData = params.data;
    },
    setOneData( state, params ) {
        state.data[params.module].oneData = params.data;
    }
};

const actions = { 
    readList({ commit, state }, params={}) {
        return globals()
            .LocalApi.get(state.data[params.module].apiEndpoint, {
                params: params.params
            })
            .then(res => {
                if(params.saveState==undefined||params.saveState)
                    commit("setListData", {
                        module: params.module,
                        data: res.data.data
                    });
                return res.data.data;
            });
    },
    readOne({ commit, state }, params) {
        return globals()
            .LocalApi.get(state.data[params.module].apiEndpoint + params.id)
            .then(res => {
                if(params.saveState==undefined||params.saveState)
                    commit("setOneData", {
                        module: params.module,
                        data: res.data.data
                    });
                return res.data.data;
            });
    },
    create({ dispatch, state }, params) {
        return globals()
            .LocalApi.post(state.data[params.module].apiEndpoint, params.data)
            .then(res => {
                return params.reload==undefined||!params.reload?res.data.data:dispatch("readList",{module: params.module});
            });
    },
    update({ dispatch, state }, params) {
        return globals()
            .LocalApi.put(state.data[params.module].apiEndpoint + params.id, params.data)
            .then(res => {
                return params.reload==undefined||!params.reload?res.data.data:dispatch("readList",{module: params.module});
            });
    },
    delete({ dispatch, state }, params) {
        return globals()
            .LocalApi.delete(state.data[params.module].apiEndpoint + params.id)
            .then(res => {
                return params.reload==undefined||!params.reload?res.data.data:dispatch("readList",{module: params.module});
            });
    },
};

const storeRepo = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};

export default storeRepo;
