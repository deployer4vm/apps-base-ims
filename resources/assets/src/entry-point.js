window._ = {};
window._.merge = require("lodash/merge");
window._.forEach = require("lodash/forEach");

// Polyfills
require("core-js/modules/es6.array.fill");
require("core-js/modules/es6.array.iterator");
require("core-js/modules/es6.object.assign");
require("core-js/modules/es6.object.keys");
require("core-js/modules/es6.promise");
require("core-js/modules/es6.string.includes");
require("core-js/modules/es6.symbol");
require("core-js/modules/es7.array.includes");
require("core-js/modules/es7.object.entries");
require("core-js/modules/es7.promise.finally");
require("core-js/modules/es7.symbol.async-iterator");

import Vue from "vue";
import axios from "axios";

//register vue on window level
window.Vue = Vue;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

/*
load all application config
*/
window.appconfig = {
    system: {
        ...require("../../../app/MainApp/config/system.json"),
        ...require("../../../app/MainApp/config/systemEnv.json")
    },
    client: require("../../../app/MainApp/config/client.json"),
    packageLocal: {},
    package: require("../../../app/MainApp/config/package.json"),
    listener: require("../../../app/MainApp/config/listener.json")
};

let varPackageLocal = require("../../../app/MainApp/config/packageLocal.json");
let varPackageLocalEnv = require("../../../app/MainApp/config/packageLocalEnv.json");

_.forEach(window.appconfig.package, (value, index) => {
    window.appconfig.packageLocal[value.package_namespace] = _.merge(
        _.merge(
            window.appconfig.package[value.package_namespace],
            varPackageLocal[value.package_namespace]
        ),
        varPackageLocalEnv[value.package_namespace]
    );
});

/**
 * Load Vue.js app
 */

require("./main.js");
