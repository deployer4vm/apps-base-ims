//convert array to query string
function params(object) {
    var parameters = [];
    for (var property in object) {
        if (object.hasOwnProperty(property)) {
            parameters.push(encodeURI(property + '=' + object[property]));
        }
    }

    return parameters.join('&');
}

//parsing error local api
function localApiErrorPars(res) {
    
    let err = { status: 400, message: "request error" , errors: []};
    //jika error server
    if (!res.data) {
        err.message = res.message;
    } else {
        err.message = res.data.message;
        err.status = res.data.status;

        if(res.data.errors){
            err.errors = res.data.errors;
            _.forEach(res.data.errors,(v,i)=>{
                err.message += "<br> - " + v[0];
            });
        }
    }

    return err;
}