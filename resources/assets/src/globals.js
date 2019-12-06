import layoutHelpers from '@/helpers/layout.js';
import UserAuth from '@/helpers/userauth.js';
import Web from '@/helpers/web.js';
import Trans from '@/helpers/trans.js';
import AppConfig from '@/appconfig.js';
import Helper from '@/helpers/helper.js';

// import _default from 'vuex';

import {conformToMask} from 'node_modules/vue-text-mask';
import * as textMaskAddons from 'node_modules/text-mask-addons/dist/textMaskAddons';

let web = Web;
web.endpoint = AppConfig.endpoint;

/*
set local Api
*/
var localapi = new axios.create();
/**
 * parsing error local api
 * @param object res response axios
 **/
localapi.parseError = function (errResponse) {
    
    let err = { status: 400, message: "request error" , errors: []};
    //jika error server
    if (!errResponse.data) {
        err.message = errResponse.message;
    } else {
        err.message = errResponse.data.message;
        err.status = errResponse.data.status;

        if(errResponse.data.errors){
            err.errors = errResponse.data.errors;
            _.forEach(errResponse.data.errors,(v,i)=>{
                if(v!=true){
                    if(v instanceof Object){
                        _.forEach(v,(v2,i2)=>{
                            err.message += "<br> - " + v2;
                        });
                    }else{
                        err.message += "<br> - " + v;
                    }
                }
            });
        }
    }

    return err;
};
localapi.errAlertText = {
    title: "Alert",
    text: "Session Expired"
}
localapi.defaults.baseURL = "/";//AppConfig.client.endpoint[AppConfig.system.mode]["domain"];
localapi.defaults.headers.get["Accepts"] = "application/json";
localapi.defaults.headers.common['Content-Type'] = 'multipart/form-data';
localapi.interceptors.response.use((response) => response, (error) => {    
    if(error.response.data && error.response.data.message && error.response.data.errors && error.response.data.status) {
        let err = localapi.parseError(error.response);
        //jika error token expired/auth gagal maka logoutkan
        if(err.status == 401){
            Web.showAlert({
                type: "warning",
                title: localapi.errAlertText.title,// Trans.get("alert.form_must_complete_title"),
                text: localapi.errAlertText.text//Trans.get("alert.session_expired")
            });
            UserAuth.logout();
        }else{
            err.errClass = error;
            throw err;
        }
    }else{
        throw error;
    }
});

/*
jika multi tenant aktif
*/
if(AppConfig.system.web_admin.multitenant.active){
    var newEndpoint = {};
    _.forEach(AppConfig.endpoint.admin,(v,i)=>{
        newEndpoint[i] = "/:group_app" + v;
    });
    AppConfig.endpoint.admin = newEndpoint;
}

/**
 * Downloader
 */
let downloadVar = {
    path: ''
}

/**
 * Formater
 */
let formater = {}
/**
 * config format
 */
formater.config = {
    currencyMask : {
        prefix: 'Rp. ',allowDecimal : true, decimalSymbol:',',thousandsSeparatorSymbol: '.',decimalLimit: 2
    },
    numberMask : {
        prefix: '',allowDecimal : false, thousandsSeparatorSymbol: '.'
    },
    decimalMask : {
        prefix: '',allowDecimal : true, decimalSymbol:',',thousandsSeparatorSymbol: '.',decimalLimit: 2
    },
    formatDate : 'DD-MM-YYYY'
};
/**
 * format config textmaskaddons nya
 */
formater.format = {
    setDecimalLimit(limit) {
        formater.config.currencyMask.decimalLimit = limit;
        formater.config.decimalMask.decimalLimit = limit;
        formater.format.currencyMask = textMaskAddons.createNumberMask(formater.config.currencyMask);
        formater.format.decimalMask = textMaskAddons.createNumberMask(formater.config.decimalMask);
    },
    setThousandsSeparatorSymbol(simbol) {
        simbol=simbol==','?',':'.';
        formater.config.currencyMask.thousandsSeparatorSymbol = simbol;
        formater.config.numberMask.thousandsSeparatorSymbol = simbol;
        formater.config.decimalMask.thousandsSeparatorSymbol = simbol;
        formater.format.currencyMask = textMaskAddons.createNumberMask(formater.config.currencyMask);
        formater.format.numberMask = textMaskAddons.createNumberMask(formater.config.numberMask);
        formater.format.decimalMask = textMaskAddons.createNumberMask(formater.config.decimalMask);
    },
    setDecimalSymbol(simbol) {
        simbol=simbol==','?',':'.';
        formater.config.currencyMask.decimalSymbol = simbol;
        formater.config.decimalMask.decimalSymbol = simbol;
        formater.format.currencyMask = textMaskAddons.createNumberMask(formater.config.currencyMask);
        formater.format.decimalMask = textMaskAddons.createNumberMask(formater.config.decimalMask);
    },
    currencyMask: textMaskAddons.createNumberMask(formater.config.currencyMask),        
    numberMask: textMaskAddons.createNumberMask(formater.config.numberMask),//mask without decimal
    decimalMask: textMaskAddons.createNumberMask(formater.config.decimalMask)//mask with decimal
};

/**
 * format function nya
 */

formater.formatPrice = function(number) {
    if(isNaN(number))number = formater.resetNumber(number);

    if(formater.config.currencyMask.decimalSymbol == ','){
        number = String(number);
        number = number.replace(/\./g,',');
    }else{
        number = String(number);
    }
    return conformToMask(
            number,
            formater.format.currencyMask,
            {guide: false}
        ).conformedValue;
};

formater.formatNumber = function(number) {
    if(isNaN(number))number = formater.resetNumber(number);
    return conformToMask(
            String(number),
            formater.format.numberMask,
            {guide: false}
        ).conformedValue;
};

formater.formatDecimal = function(number) {
    if(isNaN(number))number = formater.resetNumber(number);

    if(formater.config.decimalMask.decimalSymbol == ','){
        number = String(parseFloat(number));
        number = number.replace(/\./g,',');
    }else{
        number = String(number);
    }
    return conformToMask(
            number,
            formater.format.decimalMask,
            {guide: false}
        ).conformedValue;
};

formater.formatDate = function(dateString) {
    return moment(dateString).format(formater.config.formatDate);
};

formater.resetNumber = function(number) {
    number = String(number);
    number = number.replace(formater.config.currencyMask.prefix, "");
    number = number.replace(/[^0-9,.]/g, "");

    if(formater.config.decimalMask.decimalSymbol == ','){
        number = number.replace(/\./g, "").replace(/,/g,'.');
    }else{
        number = number.replace(/,/g, "");
    }
    return parseFloat(number);
};

export default function () {
    return {
        // Public url
        publicUrl: '/',
        
        // Layout helpers
        layoutHelpers,

        //config app
        AppConfig,

        //translation / locale
        Trans,

        //general helper
        Helper,

        //local api
        LocalApi: localapi,

        //user auth helper
        UserAuth,

        //formater
        Format: formater,

        //downloader
        download: function(path,filename) {    
            let docUrl = this.downloadVar.path + path;
            axios({
                method: 'get',
                url: docUrl,
                responseType: 'arraybuffer'
            })
            .then(response => { 
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename); //or any other extension
                document.body.appendChild(link);
                link.click();        
            })
            .catch((err) => {
                console.log('download error : ',err);
                web.showAlert({text: "Download Error",type: "warning"});
            });
        },
        downloadVar: downloadVar,
        setDownloadPath(path){
            this.downloadVar.path = path;
        },
        
        //general web helper
        Web: web,

        // Check for RTL layout
        get isRTL () {
            return document.documentElement.getAttribute('dir') === 'rtl' ||
            document.body.getAttribute('dir') === 'rtl'
        },

        // Check if IE
        get isIEMode () {
            return typeof document['documentMode'] === 'number'
        },

        // Check if IE10
        get isIE10Mode () {
            return this.isIEMode && document['documentMode'] === 10
        },

        // Layout navbar color
        get layoutNavbarBg () {
            return this.AppConfig.system.web_admin.navbar_bgcolor;
        },

        // Layout sidenav color
        get layoutSidenavBg () {
            return this.AppConfig.system.web_admin.sidenav_bgcolor;
        },

        // Layout footer color
        get layoutFooterBg () {
            return this.AppConfig.system.web_admin.footer_bgcolor;
        },

        
        // Animate scrollTop
        scrollTop (to, duration, element = document.scrollingElement || document.documentElement) {
            if (element.scrollTop === to) return
            const start = element.scrollTop
            const change = to - start
            const startDate = +new Date()

            // t = current time; b = start value; c = change in value; d = duration
            const easeInOutQuad = (t, b, c, d) => {
                t /= d / 2
                if (t < 1) return c / 2 * t * t + b
                t--
                return -c / 2 * (t * (t - 2) - 1) + b
            }

            const animateScroll = () => {
                const currentDate = +new Date()
                const currentTime = currentDate - startDate
                element.scrollTop = parseInt(easeInOutQuad(currentTime, start, change, duration))
                if (currentTime < duration) {
                    requestAnimationFrame(animateScroll)
                } else {
                    element.scrollTop = to
                }
            }

            animateScroll()
        }
    }
}
