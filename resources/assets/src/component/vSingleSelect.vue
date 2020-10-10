<template>
    <multiselect 
        v-model="modelDataTmp"
        @select="selectData"
        :allow-empty="inlineAllowEmpty"
        :options="options"
        :disabled="disabled"
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
        'modelData',
        'disabled'
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
        if(this.modelData>0)this.setSelected(this.modelData);
    },
    watch: {
        'modelData': function(v) {
            this.setSelected(this.modelData);
        }
    },
    methods: {
        selectData(selectedOption){
            this.$emit('onSelect', selectedOption.value);
        },
        setSelected(value) {     
            this.modelDataTmp = this.options.find(function( d ) {
                return d.value == value
            });
        }
    }
};
</script>