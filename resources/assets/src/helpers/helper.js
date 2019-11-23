/**
 * General global function this.Helper / globals().Helper
 */
export default {
    /**
     * convert array to query string
     **/
    params(object) {
        var parameters = [];
        for (var property in object) {
            if (object.hasOwnProperty(property)) {
                parameters.push(encodeURI(property + '=' + object[property]));
            }
        }

        return parameters.join('&');
    },
    //convert array (3 level) to FormData object
    convertToFormData(form) {                   
        let formData = new FormData();   
        _.forEach(form,(v,k)=>{
            if(v instanceof Object && !(v instanceof String)){
                if(v instanceof File){
                    formData.append(k, v);                                
                }else{
                    _.forEach(v,(v2,k2)=>{
                        if(v2 instanceof Object && !(v2 instanceof String)){
                            if(v2 instanceof File){
                                formData.append(k+'['+k2+']', v2);                                
                            }else{
                                _.forEach(v2,(v3,k3)=>{   

                                    if(v3 instanceof Object && !(v3 instanceof String)){
                                        if(v3 instanceof File){

                                            formData.append(k+'['+k2+']'+'['+k3+']', v3);                                
                                        }else{
                                            
                                            _.forEach(v3,(v4,k4)=>{
                                                if(v4!=null)
                                                    formData.append(k+'['+k2+']'+'['+k3+']'+'['+k4+']', v4);
                                            });
                                        }
                                    }else{
                                        if(v3!=null)
                                            formData.append(k+'['+k2+']'+'['+k3+']', v3);
                                    }
                                });
                            }
                        }else{
                            if(v2!=null)
                                formData.append(k+'['+k2+']', v2);
                        }
                        
                    });

                }
            }else{
                if(v!=null)
                    formData.append(k, v);                            
            }
        });     
        return formData;    
        
    }
}