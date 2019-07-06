import layoutHelpers from '@/helpers/layout.js';
import UserAuth from '@/helpers/userauth.js';
import Web from '@/helpers/web.js';
import Trans from '@/helpers/trans.js';
import AppConfig from '@/appconfig.js';

let web = Web;
web.endpoint = AppConfig.endpoint;

/*
set local Api
*/
var localapi = new axios.create();
localapi.defaults.baseURL = AppConfig.client.endpoint[AppConfig.system.mode]["domain"];
localapi.defaults.headers.get["Accepts"] = "application/json";

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
    LocalApi: localapi,
    UserAuth,
    
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
