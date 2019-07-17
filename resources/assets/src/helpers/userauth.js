/*
class helper untuk mengakses Auth, Keterangan routing serta templating.
seluruh class disini bisa diakses via window dan diinitialize dari vue instace utama.
*/

//diakses via window.UserAuth
export default {
    store: null,
    router: null,
    //implement acl
    implementAcl() {
        return this.store.dispatch('implementAcl');
    },
    hasAccess(key) {
        let access = 1;
        try {
            access = eval("this.store.getters.getAuthRole.rule." + key);
        } catch (err) {
            access = 1;
        }
        return access==1?true:false;
    },
    //=================================================
    login(userCredential){
        userCredential.group_app = this.router.currentRoute.params.group_app;
        return this.store.dispatch('login',userCredential).then((res)=>{            
            return res;
        });
    },
    //logoutkan session yg sekarang
    // return promise
    logout(goToLogin=true){
        this.store.dispatch('logout');
        if(goToLogin){
            this.router.push({name:"login"});
        }
    },
    //cek apakah sedang login atau tidak
    isLogin(){
        return this.store.getters.isLogin?true:false;
    },
    //get data user yang sedang login
    getUser(field=null){
        if(this.store.state.auth.user!=null){
            if(field == null) return this.store.state.auth.user;
            return this.store.state.auth.user[field];
        }
        return null;
        
    },
    getToken() {
        return this.store.getters.getAuthToken;
    },
    getAuthRole() {
        return this.store.getters.getAuthRole;
    },
    //----------go to------    
    goToLogin() {
        this.router.push({name: "login"});
    },   
    goToForgotpassword() {
        this.router.push({name: "forgotpassword"});
    },
    goToRegister() {
        this.router.push({name: "register"});
    },
    goToMyProfile() {
        this.router.push({name: "myprofile"});
    },
    goToDashboard() {
        this.router.push({name: "dashboard"});
    }
};
