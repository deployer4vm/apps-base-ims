<template>
    <multiselect 
        v-model="modelDataTmp" 
        @select="onSelect" 
        @search-change="onSearhChange"
        :allow-empty="inlineAllowEmpty" 
        :options="options" 
        :disabled="disabled" 
        :placeholder="inlinePlaceholder" 
        :selectLabel="inlineSelectLabel" 
        :deselectLabel="inlineDeselectLabel" 
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
        'selectLabel',
        'deselectLabel',
        'track-by',
        'modelData',
        'disabled'
    ],    
    data: () => ({
        modelDataTmp: {},
        inlineLabel: '',
        inlineSelectLabel: '',
        inlineDeselectLabel: '',
        inlineTrackBy: '',
        inlineAllowEmpty: '',
        inlinePlaceholder: '',
    }),
    created(){
        this.inlineLabel = this.label==undefined?'text':this.label;
        this.inlineTrackBy = this.trackBy==undefined?'value':this.trackBy;
        this.inlineAllowEmpty = this.allowEmpty==undefined?false:this.allowEmpty;
        this.inlineSelectLabel = this.selectLabel==undefined?'Press enter to select':this.selectLabel;
        this.inlineDeselectLabel = this.deselectLabel==undefined?'Press enter to remove':this.deselectLabel;
        this.inlinePlaceholder = this.placeholder==undefined?'Select':this.placeholder;
        // if(this.modelData>0)
        this.setSelected(this.modelData);
    },
    watch: {
        'modelData': function(v) {
            this.setSelected(this.modelData);
        }
    },
    methods: {
        onSelect(selectedOption){
            this.$emit('onSelect', selectedOption.value);
        },
        onSearhChange(query){
            this.$emit('onSearhChange', query);
        },
        setSelected(value) {     
            this.modelDataTmp = this.options.find(function( d ) {
                return d.value == value
            });
        }
    }
};
</script>