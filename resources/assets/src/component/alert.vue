<template>    
    <span>
        <template v-for="(v,i) in allertData">
            <b-alert show dismissible :variant="v.type" @dismissed="dismissAlert(i)" :key="i">
                {{ v.text }}
            </b-alert>
        </template>
    </span>
</template>
<script>
export default {
    name: "syncomponent-alert",
    computed: {
        allertData() {
            return this.$store.state.template.alertData;
        }
    },
    methods: {
        dismissAlert(k) {
            this.$store.commit('deleteAlert',k);
        }
    }
}
</script>
