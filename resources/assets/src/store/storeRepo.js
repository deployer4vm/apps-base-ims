import globals from "@/globals";
import storeConfig from "../../../../app/MainApp/resources/js/store/storeConfig";

const state = {
    defData: {            
        module:'',
        apiEndpoint : '',
        listDataParams : {},
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
    state.data[k].listData = JSON.parse(JSON.stringify(state.defData.listData));
    state.data[k].listDataParams = {};//JSON.parse(JSON.stringify(state.defData.listDataParams))
    state.data[k].oneData = {};//JSON.parse(JSON.stringify(state.defData.oneData))
});

const getters = {
    
};

const mutations = {
    setListData( state, params ) {
        state.data[params.module].listData = params.data;
        state.data[params.module].listDataParams = JSON.parse(JSON.stringify(params.params));
    },
    setOneData( state, params ) {
        state.data[params.module].oneData = params.data;
    }
};

const actions = { 
    readList({ commit, state }, params={}) {
        let prefix = '';
        if(state.data[params.module].pathPrefix)
            prefix = globals().Helper.replaceAttribute(state.data[params.module].pathPrefix,params);
        
        return globals()
            .LocalApi.get(state.data[params.module].apiEndpoint + prefix, {
                params: params.params?params.params:{}
            })
            .then(res => {
                if(params.saveState==undefined||params.saveState)
                    commit("setListData", {
                        module: params.module,
                        data: res.data.data,
                        params: params.params?params.params:{}
                    });
                return res.data.data;
            });
    },
    readOne({ commit, state }, params) {
        let prefix = '';
        if(state.data[params.module].pathPrefix)
            prefix = '/' + globals().Helper.replaceAttribute(state.data[params.module].pathPrefix,params);

        return globals()
            .LocalApi.get(
                state.data[params.module].apiEndpoint + params.id + prefix,
                {
                    params: params.params?params.params:{}
                }
            )
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
        let prefix = '';
        if(state.data[params.module].pathPrefix)
            prefix = globals().Helper.replaceAttribute(state.data[params.module].pathPrefix,params);
            
        return globals()
            .LocalApi.post(
                state.data[params.module].apiEndpoint + prefix, 
                params.data
            )
            .then(res => {
                return params.reload==undefined||!params.reload?res.data.data:dispatch("readList",{module: params.module});
            });
    },
    update({ dispatch, state }, params) {        
        let prefix = '';
        if(state.data[params.module].pathPrefix)
            prefix = '/' + globals().Helper.replaceAttribute(state.data[params.module].pathPrefix,params);

        return globals()
            .LocalApi.put(
                state.data[params.module].apiEndpoint + params.id + prefix, 
                params.data
            )
            .then(res => {
                return params.reload==undefined||!params.reload?res.data.data:dispatch("readList",{module: params.module});
            });
    },
    delete({ dispatch, state }, params) {
        let prefix = '';
        if(state.data[params.module].pathPrefix)
            prefix = '/' + globals().Helper.replaceAttribute(state.data[params.module].pathPrefix,params);

        return globals()
            .LocalApi.delete(state.data[params.module].apiEndpoint + params.id + prefix)
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
