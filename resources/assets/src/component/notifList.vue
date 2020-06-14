<template> 
    <b-card 
        v-if="notif.summary.unread_count != 0"
        no-body class="mb-3"
        border-variant="info"
    >
            
        <b-card-header class="with-elements bg-info text-white">
            <span class="card-header-title mr-2">{{Trans.get('notif.unread_notification')}}</span>
            <div class="card-header-elements ml-md-auto">
                <b-badge pill variant="danger">{{notif.summary.unread_count}}</b-badge>
            </div>
        </b-card-header>

        <b-list-group flush>
            <b-list-group-item class="media d-flex align-items-center" 
                :to="{name: item.link_web.route, params: item.link_web.parameter}" 
                v-for="item in notif.notification" :key="'navbarnotif-' + item.id"
            >
                <div class="ui-icon ui-icon-sm ion ion-ios-text border-0 text-white bg-danger"></div>
                <div class="media-body line-height-condenced ml-3">
                    <div class="font-weight-bold">{{item.data.subject}}</div>
                    <div class="small mt-1">{{item.data.description}}</div>
                    <div class="small mt-1">{{item.created_at}}</div>
                </div>
            </b-list-group-item>
        </b-list-group>

        <router-link :to="{name: 'notification'}"
            class="d-block text-center small p-2 my-1 text-light"
        >
            {{Trans.get('notif.show_all_notification')}}
        </router-link>

    </b-card>
</template>
<script>
/**
 * NOTIF LIST DI DASHBOARD
 */
export default {
    props: ['limit'],
    data() {
        return {           
            noitfLimit:3,
            notif:{
                notification: [],
                summary: {
                    count: 0,
                    unread_count: 0
                }
            }
        };
    },
    created() {        
        this.noitfLimit = this.limit?this.limit:this.noitfLimit;
        if(this.UserAuth.isActive())
            this.loadNotif();    
    },
    methods: {
        loadNotif() {
            var that = this;
            let filterParams = {params: {limit: this.noitfLimit}};
            this.LocalApi.get(this.AppConfig.endpoint.api.moduser + "/notification" , filterParams)
                .then(res => {
                    if(res)
                        that.notif = res.data.data;
                });
        }
    }
}
</script>
