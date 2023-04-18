<template>
    <div class="d-flex justify-content-between align-items-end w-100 mb-0 pb-3 border-bottom flex-wrap">
        <div class="d-inline-flex align-items-start flex-column">
            <b-btn
                v-if="showBack"
                class="btn btn-sm w-icon btn-outline-secondary mr-2"
                @click="goBack"
            >
                <i class="fi fi-rr-arrow-left"></i><span>{{ Trans.get("lang.back") }}</span>
            </b-btn>
            <h4 class="m-0 mt-2">
                {{ pageTitle }}
            </h4>
        </div>
        <b-breadcrumb @click="breadcrumbLink" class="mb-0 mt-2" :items="Web.getBreadcrumb()" />
    </div>
</template>
<script>
export default {
    name: "header-breadcrumb",
    props:{
        pageTitle: {
            default() {
                return '';
            }
        },
        showBack: {
            default() {
                return true;
            }
        },
        // bisa diiisi string path atau object router
        backPath: {
            default() {
                return '/';
            }
        },
        // -- khusus embed iframe
        // true jika link2 di header di alihkan ke top window nya (halaman utama tempat aplikasi diload di iframe)
        backToTopWindow: {
            default() {
                return false;
            }
        },
        breadcrumbToTopWindow: {
            default() {
                return false;
            }
        },
    },
    methods:{
        goBack() {
            if(this.backPath){
                if(this.backToTopWindow && typeof this.topWindowHref == 'function'){
                    this.topWindowHref(this.backPath);
                }else{
                    this.$router.push(this.backPath);
                }
            }else{
                this.$router.go(-1);
            }
        },
        breadcrumbLink(ev) {
            if(this.breadcrumbToTopWindow){
                this.linkTopWindowHref(ev);
            }
        }
    }
};
</script>
