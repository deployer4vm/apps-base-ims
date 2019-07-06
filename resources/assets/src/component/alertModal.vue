<template>    
    <span>
        <b-modal size="sm" scrollable centered id="alert-modals" 
            @show="showModal"
            @hidden="closeModal"
            @ok="handleOk"
            >
            <div slot="modal-title">
                {{ title }}
            </div>

            <div v-html="text"></div>
            
            <template slot="modal-footer" slot-scope="{ ok, cancel }">
                <b-button size="sm" variant="secondary" @click="cancel()">
                    {{ modalButtonCancel }}
                </b-button>
                <b-button size="sm" variant="primary" @click="ok()">
                    {{ modalButtonOk }}
                </b-button>
            </template>
        </b-modal>
    </span>
</template>

<style>
.modal-footer, .modal-header,  .modal-body {
    padding: 15px;
}
</style>

<script>
export default {
    computed: {
        title() {
            return this.$store.state.template.alertModal.title;
        },
        text() {
            return this.$store.state.template.alertModal.text;
        },
        modalButtonCancel() {
            return this.$store.state.template.alertModal.modalButtonCancel;
        },
        modalButtonOk() {
            return this.$store.state.template.alertModal.modalButtonOk;
        }
    },
    methods: {
        showModal() {
            if(typeof this.$store.state.template.alertModal.onShow == 'function')
                this.$store.state.template.alertModal.onShow();
        },
        closeModal() {
            if(typeof this.$store.state.template.alertModal.onClose == 'function')
                this.$store.state.template.alertModal.onClose();
        },
        handleOk() {
            if(typeof this.$store.state.template.alertModal.onOk == 'function')
                this.$store.state.template.alertModal.onOk();
        }
    }
}
</script>
