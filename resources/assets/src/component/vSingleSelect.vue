
<template>
    <multiselect 
        v-model="modelDataTmp"
        @select="selectData"
        :allow-empty="inlineAllowEmpty"
        :options="options"
        :placeholder="inlinePlaceholder"
        :track-by="inlineTrackBy"
        :label="inlineLabel"
    />
</template>
<style src="node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>
<script>
import Multiselect from "node_modules/vue-multiselect";
export default {
    components: {
        Multiselect,
    },
    props:[
        'allow-empty',
        'label',
        'options',
        'placeholder',
        'track-by',
        'modelData'
    ],    
    data: () => ({
        modelDataTmp: {},
        inlineLabel: '',
        inlineTrackBy: '',
        inlineAllowEmpty: '',
        inlinePlaceholder: '',
    }),
    created(){
        this.inlineLabel = this.label==undefined?'text':this.label;
        this.inlineTrackBy = this.trackBy==undefined?'value':this.trackBy;
        this.inlineAllowEmpty = this.allowEmpty==undefined?false:this.allowEmpty;
        this.inlinePlaceholder = this.placeholder==undefined?'Select':this.placeholder;
    },
    watch: {
        'modelData': function(v) {
            var that = this;            
            this.modelDataTmp = this.options.find(function( d ) {
                return d.value == that.modelData
            });
        }
    },
    methods: {
        selectData(selectedOption){
            this.$emit('onSelect', selectedOption.value);
        }
    }
};
</script>
