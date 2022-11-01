/**
 * 
 */
export default {
    store: null,
    module: '',
    setModule(module) {
        this.module = module;
        return this;
    },
    //--------
    //getter
    get listData(){
        return this.store.state.storeRepo.data[this.module].listData;
    },
    get oneData(){
        return this.store.state.storeRepo.data[this.module].oneData;
    },
    //
    readList(params){
        params.module = this.module;
        return this.store.dispatch("storeRepo/readList", params);
    },
    readOne(params){
        params.module = this.module;
        return this.store.dispatch("storeRepo/readOne", params);
    },
    create(params){
        params.module = this.module;
        return this.store.dispatch("storeRepo/create", params);
    },
    update(params){
        params.module = this.module;
        return this.store.dispatch("storeRepo/update", params);
    },
    delete(params){
        params.module = this.module;
        return this.store.dispatch("storeRepo/delete", params);
    },
}