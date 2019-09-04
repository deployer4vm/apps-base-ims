import layoutHelpers from '@/helpers/layout.js';
import UserAuth from '@/helpers/userauth.js';
import Web from '@/helpers/web.js';
import Trans from '@/helpers/trans.js';
import AppConfig from '@/appconfig.js';
// import _default from 'vuex';

import {conformToMask} from 'node_modules/vue-text-mask';
import * as textMaskAddons from 'node_modules/text-mask-addons/dist/textMaskAddons';

let web = Web;
web.endpoint = AppConfig.endpoint;

/*
set local Api
*/
var localapi = new axios.create();
localapi.defaults.baseURL = "/";//AppConfig.client.endpoint[AppConfig.system.mode]["domain"];
localapi.defaults.headers.get["Accepts"] = "application/json";
localapi.defaults.headers.common['Content-Type'] = 'multipart/form-data';

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
        prefix: 'Rp. ',allowDecimal : true, decimalSymbol:',',thousandsSeparatorSymbol: '.'
    },
    numberMask : {
        prefix: '',allowDecimal : true, decimalSymbol:',',thousandsSeparatorSymbol: '.'
    },
    formatDate : 'DD-MM-YYYY'
};
/**
 * format config textmaskaddons nya
 */
formater.format = {
    currencyMask: textMaskAddons.createNumberMask(formater.config.currencyMask),        
    numberMask: textMaskAddons.createNumberMask(formater.config.numberMask)
};
/**
 * format function nya
 */
formater.formatPrice =  function(number) {
    if(this.config.currencyMask.decimalSymbol == ','){
        number = String(number);
        number = number.replace('.',',');
    }
    return conformToMask(
            String(number),
            this.format.currencyMask,
            {guide: false}
        ).conformedValue;
};

formater.formatNumber = function(number) {
    if(this.config.numberMask.decimalSymbol == ','){
        number = String(number);
        number = number.replace('.',',');
    }
    return conformToMask(
            number,
            this.format.numberMask,
            {guide: false}
        ).conformedValue;
};

formater.formatDate = function(dateString) {
    return moment(dateString).format(this.config.formatDate);
}

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
