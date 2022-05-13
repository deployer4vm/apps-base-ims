<template>
    <div>
        <template v-for="(item, i) in menu">
            <div :key="i" class="my-4">

                <h5 class="text-nowrap font-weight-normal" v-if="item.title">{{ item.title }}</h5>

                <div class="row">
                    <div class="col-md-4" v-for="(data, j) in item.list" :key="j">
                        <div class="d-flex card align-items-left">
                            <div class="card-body media align-items-left text-body">
                                <i :class="data.icon + ' display-4 d-block text-primary'"></i>
                                <span class="media-body d-block ml-3">
                                    <span class="text-big">{{ data.title }}</span><br>
                                    <small class="text-muted">{{ data.description }}</small>
                                </span>
                                <router-link 
                                    :to="{name: data.route.name}" v-if="data.route.name"
                                    class="btn btn-sm btn-info">
                                    Go <span class="ion ion-ios-arrow-forward" />
                                </router-link>
                                <b-btn 
                                    v-else
                                    @click="topWindowHref(data.route)" 
                                    size="sm" 
                                    variant="info">
                                    Go <span class="ion ion-ios-arrow-forward" />
                                </b-btn>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "home-menu",
        props: ["menu"]
    }
</script>