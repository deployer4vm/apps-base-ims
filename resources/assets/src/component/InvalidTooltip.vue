<template>
    <div class="invalid-tooltip" v-if="inputItem.$error">
        <ul class="m-0 pl-3">
            <li v-for="(item, key) in inputItem.$params" v-if="!inputItem[key]">
                {{ alertItem[key] }}
            </li>
        </ul>                
    </div>
</template>
<script>
export default {
    data() {
        return {
            'alertItem': {}
        }
    },
    props:['inputItem','customAlert','fieldName'],
    created() {
        _.forEach(this.inputItem.$params,(v,key)=>{
            if(this.customAlert == undefined || this.customAlert[key]==undefined){
                this.alertItem[key] = this.Trans.get('validation.' + key, {attribute: this.fieldName});
                // if(theAlert == 'validation.' + key){
                //     this.alertItem[key] = Trans.get('validation.' + key, {attribute: fieldName});
                // }else{
                //     this.alertItem[key] = theAlert;
                // }
            }else{
                this.alertItem[key] = this.customAlert[key];
            }
            
        });
    }
}
</script>
