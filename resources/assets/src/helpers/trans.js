/*
language helper
diakses via window.AppTemplate atau langsung AppTemplate
*/
export default {
    store: null,
    router: null,
    allLang: null,
    //load language file from cache if exist, reload from server if not exist
    loadLang() {
        if(!this.store.getters.isLangSet){
            this.store.dispatch('reloadLang').then((val)=>{
                this.allLang = this.store.getters.getLang;
            });
        }else{
            this.allLang = this.store.getters.getLang;
        }
    },
    //force reload languange from server
    reLoadLang() {
        this.store.dispatch('reloadLang').then((val)=>{
            this.allLang = this.store.getters.getLang;
        });       
    },
    get(langKey, replace = {}) {
        let lang = "";
        try {
            lang = eval("this.allLang." + langKey);
        } catch (err) {
            lang = langKey;
        }
        _.forEach(replace, (v, k) => {
            lang = lang.replace(":" + k, v);
        });
        return lang;
    }
};
