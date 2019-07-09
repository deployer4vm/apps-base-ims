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
    props:[
        'inputItem',//object input vuelidate nya, misal : $v.form.password
        'customAlert',
        'fieldName',//text caption nama fieldnya
        'otherFieldName' //text caption nama field nama field
        ],
    created() {
        _.forEach(this.inputItem.$params,(v,key)=>{
            if(this.customAlert == undefined || this.customAlert[key]==undefined){
                let attr = {attribute: this.fieldName};
                let langKey = key;
                switch (key) {
                    case 'minLength':
                        langKey = 'min.numeric';
                        attr.min = v.min;
                        break;  
                    case 'maxLength':
                        langKey = 'max.numeric';
                        attr.max = v.max;
                        break;  
                    case 'sameAs':
                        langKey = 'same';
                        attr.other = this.otherFieldName;
                        break; 
                    case 'between':
                        langKey = 'between.numeric';
                        attr.min = v.min;
                        attr.max = v.max;
                        break;                
                    default:                        
                        break;
                }
                this.alertItem[key] = this.Trans.get('validation.' + langKey, attr);
            }else{
                this.alertItem[key] = this.customAlert[key];
            }
            
        });
    }
}
</script>
