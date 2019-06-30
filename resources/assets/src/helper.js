/*
class helper untuk mengakses Auth, Keterangan routing serta templating.
seluruh class disini bisa diakses via window dan diinitialize dari vue instace utama.
*/

//diakses via window.UserAuth
export const UserAuth = {
    store: null,
    /*
    detak apakah sedang login atau tidak
    */
    isLogin(){
        return store.getters.isLogin?true:false;
    },
    getUser(field){
        if(store.state.user[field])return store.state.user[field];
        return store.state.user;
    },
};

export const AppTemplate = {
    store: null,
    router: null,
    //cek apakah halaman yang diakses sekarang admin area
    isOnAdminArea(){
        return true;
    },
    
}