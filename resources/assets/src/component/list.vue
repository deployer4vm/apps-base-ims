<template>
    <div>
        <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
            <div>{{pageTitle}}</div>
            <router-link :to="{name : 'modwarehouse.master.product.createForm'}" class="btn btn-sm btn-secondary d-block" v-if="UserAuth.hasAccess(accessRuleKey, 'c')"> 
                <span class="ion ion-md-add"></span>&nbsp; {{Trans.get("lang.add_attribute",{attribute: Trans.get("wh.product")})}}
            </router-link>
        </h4>

        <b-card no-body>
            <!-- Table controls -->
            <b-card-body>
                <div class="row">
                    <div class="col">
                        Per page: &nbsp;
                        <b-select size="sm" v-model="perPage" :options="perPageOption" class="d-inline-block w-auto" />
                    </div>
                    <div class="col">
                        <b-input size="sm" placeholder="Search..." class="d-inline-block w-auto float-sm-right" v-model="searchString" />
                    </div>
                </div>
            </b-card-body>
            <!-- / Table controls -->

            <!-- Table -->
            <hr class="border-light m-0" />
            <div class="table-responsive mb-0">
                <b-table :items="listData.data" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :striped="true" :bordered="true" :current-page="curPage" :per-page="perPage" :small="true" :responsive="true" class="card-table mb-0" :empty-text="noResultsText" show-empty>
                    <template v-slot:empty="scope">
                        <h5 class="text-center mb-1 mt-1">{{ scope.emptyText }}</h5>
                    </template>

                    <template v-slot:cell(actions)="data">
                        <router-link :to="{name : 'modwarehouse.master.product.edit', params: {productId: data.item.id}}" class="btn btn-sm btn-default btn-xs icon-btn md-btn-flat" v-b-tooltip.hover.left title="Edit" v-if="UserAuth.hasAccess(accessRuleKey, 'u')">
                            <i class="ion ion-md-create"></i>
                        </router-link>
                        <b-btn @click="deleteData(data.item.id)" variant="danger btn-xs icon-btn md-btn-flat" v-b-tooltip.hover.left title="Remove" v-if="UserAuth.hasAccess(accessRuleKey, 'd')">
                            <i class="ion ion-md-close"></i>
                        </b-btn>
                    </template>
                </b-table>
            </div>

            <!-- Pagination -->
            <b-card-body class="pt-0 pb-3" v-if="listData.count">
                <div class="row">
                    <div class="col-sm text-sm-left text-center pt-3">
                        <span class="text-muted">Page {{ curPage }} of {{ totalPages }}</span>
                    </div>
                    <div class="col-sm pt-3">
                        <b-pagination class="justify-content-center justify-content-sm-end m-0" v-model="curPage" :total-rows="listData.count" :per-page="perPage" size="sm" />
                    </div>
                </div>
            </b-card-body>
            <!-- / Pagination -->
        </b-card>
    </div>
</template>

<script>
    export default {
        name: "modwarehouse-product-list",
        props: [
            "headerTitle","accessRuleKey"
        ],
        data: () => ({
            isAdd: true,//flag untuk form input, apakah proses add atau edit
            defaultModalSize: "md",
            accessRuleKey: "modwarehouse.master.product",

            // Options
            searchString: "",
            sortBy: "id",
            sortDesc: false,
            perPage: 10,
            curPage: 1,
            perPageOption: [10,20,50,100],
            noResultsText: "Loading...",
            loadParams: {},

            fields: []
        }),
        watch: {
            curPage(v) {
                this.loadList(v,this.searchString,this.sortBy,this.sortDesc);
            },
            perPage(v) {
                this.loadList(this.curPage,this.searchString,this.sortBy,this.sortDesc);
            },
            sortBy(v) {
                this.loadList(this.curPage,this.searchString,v,this.sortDesc);
            },
            sortDesc(v) {
                this.loadList(this.curPage,this.searchString,this.sortBy, v);
            },    
            searchString(v) {
                let val = v.toLowerCase();      
                var that = this;
                clearTimeout(this.suggestTimeout);
                this.suggestTimeout = setTimeout(function(){
                    that.loadList(1,val);      
                },300);
            }
        },
        computed: {
            pageTitle() {
                return this.Trans.chose(this.AppConfig.packageLocal.modwarehouse.access.children.master.children.product.caption);
            },
            formTitle() {
                return this.isAdd ? this.Trans.get("lang.add_attribute",{attribute: this.Trans.get("wh.product")}) : this.Trans.get("lang.edit_attribute",{attribute: this.Trans.get("wh.product")});
            },
            listData() {
                return this.$store.state.modwarehouseProduct.listData;
            },
            oneData() {
                return this.$store.state.modwarehouseProduct.oneData;
            },
            totalPages() {
                return Math.ceil(this.listData.count / this.perPage);
            }
        },

        methods: {            
            loadList(curPage=1,q='',orderBy=false,sortDesc=false) {
                var offset = (this.perPage * (curPage-1));
                
                this.loadParams.limit = this.perPage;
                this.loadParams.offset = offset;

                if(q!=''){
                    this.loadParams.q = q;
                }else{
                    delete this.loadParams.q;
                }
                if(orderBy!=false){
                    this.loadParams.orderBy = orderBy;
                    this.loadParams.orderType = sortDesc?'DESC':'ASC';
                }

                this.$store.dispatch("modwarehouseProduct/readList", {params: this.loadParams}).then(res => {
                    if (this.listData.count == 0) {
                        this.noResultsText = this.Trans.get("lang.no_data");
                        this.Web.showAlert({ 
                            title: this.Trans.get("alert.info_title"),
                            text: this.Trans.get("lang.no_data"), 
                            type: "info" 
                        });
                    }
                }).catch((res)=>{
                    this.Web.showAlert({ 
                        title: this.Trans.get("alert.warning_title"),
                        text: this.Trans.get("alert.read_failed",{attribute:this.Trans.get("wh.product")}) + "<br>\n" + res.message, 
                        type: "warning" 
                    });
                });
            },
            deleteData(id) {
                if (!this.UserAuth.hasAccess(this.accessRuleKey, "d")) {
                    //goto dashboard current tenant
                    this.Web.goToCurrentTenant();
                    this.Web.showAlert({ 
                        title: this.Trans.get("alert.warning_title"),
                        text: this.Trans.get("alert.access_denied"), 
                        type: "warning" 
                    });
                    return false;
                }

                this.Web.showAlert({
                    styleType: "modal",
                    type: "warning",
                    title: this.Trans.get("alert.delete_confirm_title"),
                    text: this.Trans.get("alert.delete_confirm_text"),
                    modalButtonCancel: this.Trans.get("lang.no"),
                    modalButtonOk: this.Trans.get("lang.yes"),
                    onOk: () => {
                        this.$store
                            .dispatch("modwarehouseProduct/delete", {id:id})
                            .then(res => {
                                this.Web.showAlert({ 
                                    title: this.Trans.get("alert.success_title"),
                                    text: this.Trans.get("alert.delete_success",{attribute:this.Trans.get("wh.product")}), 
                                    type: "success" 
                                });
                            })
                            .catch(res => {
                                this.Web.showAlert({ 
                                    title: this.Trans.get("alert.warning_title"),
                                    text: this.Trans.get("alert.delete_failed",{attribute:this.Trans.get("wh.product")}) + "<br>\n" + res.message, 
                                    type: "warning" 
                                });
                            });
                    }
                });
            }
        },
        created() {
            if (!this.UserAuth.hasAccess(this.accessRuleKey)) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({ 
                    title: this.Trans.get("alert.warning_title"),
                    text: this.Trans.get("alert.access_denied"), 
                    type: "warning" 
                });
                return false;
            }
            this.fields = [
                
                { key: "no", sortable: true, thStyle: "min-width: 5rem" },
                { key: "nama", sortable: true, thStyle: "min-width: 5rem" },
                { key: "keterangan", sortable: true },
                { key: "actions", label: " ", tdClass: "text-center text-nowrap" }
            
            ];

            //jika tidak punya access edit/delete maka hilangkan kolom aksi
            if (!(this.UserAuth.hasAccess(this.accessRuleKey, "u") || this.UserAuth.hasAccess(this.accessRuleKey, "d")) ) {
                this.fields.splice(2, 1);
            }

            this.Web.setNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.modwarehouse.access.caption));
            this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.modwarehouse.access.children.master.caption));
            this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.modwarehouse.access.children.master.children.product.caption));

            //reload list data
            this.loadList();
        }
    };
</script>
