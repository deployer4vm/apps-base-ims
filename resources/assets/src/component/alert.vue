<template>    
    <span>
        <template v-for="(v,i) in allertData">
            <b-alert show dismissible :variant="v.type" @dismissed="dismissAlert(i)">
                {{ v.text }}
            </b-alert>
        </template>
    </span>
</template>
<script>
export default {
    computed: {
        allertData() {
            return this.$store.state.template.alertData;
        }
    },
    methode: {
        dismissAlert(k) {
            this.$store.commit('deleteAlert',k);
        }
    }
}
</script>
