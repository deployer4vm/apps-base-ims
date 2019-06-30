(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[0],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutFooter.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/LayoutFooter.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'app-layout-footer',
  methods: {
    getLayoutFooterBg: function getLayoutFooterBg() {
      return "bg-".concat(this.layoutFooterBg);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutNavbar.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/LayoutNavbar.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'app-layout-navbar',
  props: {
    sidenavToggle: {
      type: Boolean,
      "default": true
    }
  },
  methods: {
    toggleSidenav: function toggleSidenav() {
      this.layoutHelpers.toggleCollapsed();
    },
    getLayoutNavbarBg: function getLayoutNavbarBg() {
      return this.layoutNavbarBg;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutSidenav.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/LayoutSidenav.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/vendor/libs/sidenav */ "./resources/assets/src/vendor/libs/sidenav/index.js");
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'app-layout-sidenav',
  components: {
    Sidenav: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["Sidenav"],
    SidenavLink: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["SidenavLink"],
    SidenavRouterLink: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["SidenavRouterLink"],
    SidenavMenu: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["SidenavMenu"],
    SidenavHeader: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["SidenavHeader"],
    SidenavBlock: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["SidenavBlock"],
    SidenavDivider: _vendor_libs_sidenav__WEBPACK_IMPORTED_MODULE_0__["SidenavDivider"]
  },
  props: {
    orientation: {
      type: String,
      "default": 'vertical'
    }
  },
  computed: {
    curClasses: function curClasses() {
      var bg = this.layoutSidenavBg;

      if (this.orientation === 'horizontal' && (bg.indexOf(' sidenav-dark') !== -1 || bg.indexOf(' sidenav-light') !== -1)) {
        bg = bg.replace(' sidenav-dark', '').replace(' sidenav-light', '').replace('-darker', '').replace('-dark', '');
      }

      return "bg-".concat(bg, " ") + (this.orientation !== 'horizontal' ? 'layout-sidenav' : 'layout-sidenav-horizontal container-p-x flex-grow-0');
    }
  },
  methods: {
    isMenuActive: function isMenuActive(url) {
      return this.$route.path.indexOf(url) === 0;
    },
    isMenuOpen: function isMenuOpen(url) {
      return this.$route.path.indexOf(url) === 0 && this.orientation !== 'horizontal';
    },
    toggleSidenav: function toggleSidenav() {
      this.layoutHelpers.toggleCollapsed();
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav-block'
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var perfect_scrollbar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! perfect-scrollbar */ "./node_modules/perfect-scrollbar/dist/perfect-scrollbar.esm.js");
//
//
//
//
//
//
//
//


var SideNav = __webpack_require__(/*! ./sidenav.js */ "./resources/assets/src/vendor/libs/sidenav/sidenav.js").SideNav;

/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav',
  props: {
    orientation: {
      type: String,
      "default": 'vertical'
    },
    animate: {
      type: Boolean,
      "default": true
    },
    accordion: {
      type: Boolean,
      "default": true
    },
    closeChildren: {
      type: Boolean,
      "default": false
    },
    showDropdownOnHover: {
      type: Boolean,
      "default": false
    },
    onOpen: Function,
    onOpened: Function,
    onClose: Function,
    onClosed: Function
  },
  mounted: function mounted() {
    this.orientation = this.orientation === 'horizontal' ? 'horizontal' : 'vertical';
    this.sidenav = new SideNav(this.$el, {
      orientation: this.orientation,
      animate: this.animate,
      accordion: this.accordion,
      closeChildren: this.closeChildren,
      showDropdownOnHover: this.showDropdownOnHover,
      onOpen: this.onOpen,
      onOpened: this.onOpened,
      onClose: this.onClose,
      onClosed: this.onClosed
    }, perfect_scrollbar__WEBPACK_IMPORTED_MODULE_0__["default"]);
  },
  beforeDestroy: function beforeDestroy() {
    if (this.sidenav) this.sidenav.destroy();
    this.sidenav = null;
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav-divider'
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav-header'
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav-link',
  props: {
    href: String,
    icon: String,
    target: {
      type: String,
      "default": '_self'
    },
    linkClass: {
      type: String,
      "default": ''
    },
    badge: {
      "default": null
    },
    badgeClass: {
      type: String,
      "default": ''
    },
    disabled: {
      type: Boolean,
      "default": false
    },
    active: {
      type: Boolean,
      "default": false
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav-menu',
  props: {
    icon: String,
    linkClass: {
      type: String,
      "default": ''
    },
    badge: {
      "default": null
    },
    badgeClass: {
      type: String,
      "default": ''
    },
    disabled: {
      type: Boolean,
      "default": false
    },
    active: {
      type: Boolean,
      "default": false
    },
    open: {
      type: Boolean,
      "default": false
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'sidenav-router-link',
  props: {
    to: null,
    replace: {
      type: Boolean,
      "default": false
    },
    append: {
      type: Boolean,
      "default": false
    },
    exact: {
      type: Boolean,
      "default": false
    },
    event: null,
    icon: String,
    linkClass: {
      type: String,
      "default": ''
    },
    badge: {
      "default": null
    },
    badgeClass: {
      type: String,
      "default": ''
    },
    disabled: {
      type: Boolean,
      "default": false
    },
    active: {
      type: Boolean,
      "default": false
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/lib/loader.js?!./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--12-2!./node_modules/sass-loader/lib/loader.js??ref--12-3!./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "/*\n * Container style\n */\n.ps {\n  overflow: hidden !important;\n  overflow-anchor: none;\n  -ms-overflow-style: none;\n  touch-action: auto;\n  -ms-touch-action: auto;\n}\n\n/*\n * Scrollbar rail styles\n */\n.ps__rail-x {\n  display: none;\n  opacity: 0;\n  transition: background-color 0.2s linear, opacity 0.2s linear;\n  -webkit-transition: background-color 0.2s linear, opacity 0.2s linear;\n  height: 15px;\n  /* there must be 'bottom' or 'top' for ps__rail-x */\n  bottom: 0px;\n  /* please don't change 'position' */\n  position: absolute;\n}\n.ps__rail-y {\n  display: none;\n  opacity: 0;\n  transition: background-color 0.2s linear, opacity 0.2s linear;\n  -webkit-transition: background-color 0.2s linear, opacity 0.2s linear;\n  width: 15px;\n  /* there must be 'right' or 'left' for ps__rail-y */\n  right: 0;\n  /* please don't change 'position' */\n  position: absolute;\n}\n.ps--active-x > .ps__rail-x,\n.ps--active-y > .ps__rail-y {\n  display: block;\n  background-color: transparent;\n}\n.ps:hover > .ps__rail-x,\n.ps:hover > .ps__rail-y,\n.ps--focus > .ps__rail-x,\n.ps--focus > .ps__rail-y,\n.ps--scrolling-x > .ps__rail-x,\n.ps--scrolling-y > .ps__rail-y {\n  opacity: 0.6;\n}\n.ps .ps__rail-x:hover,\n.ps .ps__rail-y:hover,\n.ps .ps__rail-x:focus,\n.ps .ps__rail-y:focus,\n.ps .ps__rail-x.ps--clicking,\n.ps .ps__rail-y.ps--clicking {\n  background-color: #eee;\n  opacity: 0.9;\n}\n\n/*\n * Scrollbar thumb styles\n */\n.ps__thumb-x {\n  background-color: #aaa;\n  border-radius: 6px;\n  transition: background-color 0.2s linear, height 0.2s ease-in-out;\n  -webkit-transition: background-color 0.2s linear, height 0.2s ease-in-out;\n  height: 6px;\n  /* there must be 'bottom' for ps__thumb-x */\n  bottom: 2px;\n  /* please don't change 'position' */\n  position: absolute;\n}\n.ps__thumb-y {\n  background-color: #aaa;\n  border-radius: 6px;\n  transition: background-color 0.2s linear, width 0.2s ease-in-out;\n  -webkit-transition: background-color 0.2s linear, width 0.2s ease-in-out;\n  width: 6px;\n  /* there must be 'right' for ps__thumb-y */\n  right: 2px;\n  /* please don't change 'position' */\n  position: absolute;\n}\n.ps__rail-x:hover > .ps__thumb-x,\n.ps__rail-x:focus > .ps__thumb-x,\n.ps__rail-x.ps--clicking .ps__thumb-x {\n  background-color: #999;\n  height: 11px;\n}\n.ps__rail-y:hover > .ps__thumb-y,\n.ps__rail-y:focus > .ps__thumb-y,\n.ps__rail-y.ps--clicking .ps__thumb-y {\n  background-color: #999;\n  width: 11px;\n}\n\n/* MS supports */\n@supports (-ms-overflow-style: none) {\n.ps {\n    overflow: auto !important;\n}\n}\n@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {\n.ps {\n    overflow: auto !important;\n}\n}\n.ps {\n  position: relative;\n}\n.ps__rail-x,\n.ps__rail-y,\n.ps__thumb-x,\n.ps__thumb-y {\n  border-radius: 10rem;\n}\n.ps__rail-x {\n  height: 0.25rem;\n}\n.ps__rail-y {\n  width: 0.25rem;\n}\n.ps__thumb-x {\n  bottom: 0;\n  height: 0.25rem;\n}\n.ps__thumb-y {\n  right: 0;\n  width: 0.25rem;\n}\n.ps__rail-x:hover,\n.ps__rail-x:focus,\n.ps__rail-x.ps--clicking,\n.ps__rail-x:hover > .ps__thumb-x,\n.ps__rail-x:focus > .ps__thumb-x,\n.ps__rail-x.ps--clicking > .ps__thumb-x {\n  height: 0.375rem;\n}\n.ps__rail-y:hover,\n.ps__rail-y:focus,\n.ps__rail-y.ps--clicking,\n.ps__rail-y:hover > .ps__thumb-y,\n.ps__rail-y:focus > .ps__thumb-y,\n.ps__rail-y.ps--clicking > .ps__thumb-y {\n  width: 0.375rem;\n}\n.default-style .ps__rail-x:hover,\n.default-style .ps__rail-y:hover,\n.default-style .ps__rail-x:focus,\n.default-style .ps__rail-y:focus,\n.default-style .ps__rail-x.ps--clicking,\n.default-style .ps__rail-y.ps--clicking {\n  background-color: rgba(24, 28, 33, 0.1);\n}\n.default-style .ps__thumb-x,\n.default-style .ps__thumb-y {\n  background-color: rgba(24, 28, 33, 0.3);\n}\n.default-style .ps__rail-x:hover > .ps__thumb-x,\n.default-style .ps__rail-y:hover > .ps__thumb-y,\n.default-style .ps__rail-x:focus > .ps__thumb-x,\n.default-style .ps__rail-y:focus > .ps__thumb-y,\n.default-style .ps__rail-x.ps--clicking > .ps__thumb-x,\n.default-style .ps__rail-y.ps--clicking > .ps__thumb-y {\n  background-color: rgba(24, 28, 33, 0.6);\n}\n.default-style .ps-inverted .ps__rail-x:hover,\n.default-style .ps-inverted .ps__rail-y:hover,\n.default-style .ps-inverted .ps__rail-x:focus,\n.default-style .ps-inverted .ps__rail-y:focus,\n.default-style .ps-inverted .ps__rail-x.ps--clicking,\n.default-style .ps-inverted .ps__rail-y.ps--clicking {\n  background-color: rgba(255, 255, 255, 0.5);\n}\n.default-style .ps-inverted .ps__thumb-x,\n.default-style .ps-inverted .ps__thumb-y {\n  background-color: rgba(255, 255, 255, 0.7);\n}\n.default-style .ps-inverted .ps__rail-x:hover > .ps__thumb-x,\n.default-style .ps-inverted .ps__rail-y:hover > .ps__thumb-y,\n.default-style .ps-inverted .ps__rail-x:focus > .ps__thumb-x,\n.default-style .ps-inverted .ps__rail-y:focus > .ps__thumb-y,\n.default-style .ps-inverted .ps__rail-x.ps--clicking > .ps__thumb-x,\n.default-style .ps-inverted .ps__rail-y.ps--clicking > .ps__thumb-y {\n  background-color: #fff;\n}\n.material-style .ps__rail-x:hover,\n.material-style .ps__rail-y:hover,\n.material-style .ps__rail-x:focus,\n.material-style .ps__rail-y:focus,\n.material-style .ps__rail-x.ps--clicking,\n.material-style .ps__rail-y.ps--clicking {\n  background-color: rgba(24, 28, 33, 0.1);\n}\n.material-style .ps__thumb-x,\n.material-style .ps__thumb-y {\n  background-color: rgba(24, 28, 33, 0.3);\n}\n.material-style .ps__rail-x:hover > .ps__thumb-x,\n.material-style .ps__rail-y:hover > .ps__thumb-y,\n.material-style .ps__rail-x:focus > .ps__thumb-x,\n.material-style .ps__rail-y:focus > .ps__thumb-y,\n.material-style .ps__rail-x.ps--clicking > .ps__thumb-x,\n.material-style .ps__rail-y.ps--clicking > .ps__thumb-y {\n  background-color: rgba(24, 28, 33, 0.6);\n}\n.material-style .ps-inverted .ps__rail-x:hover,\n.material-style .ps-inverted .ps__rail-y:hover,\n.material-style .ps-inverted .ps__rail-x:focus,\n.material-style .ps-inverted .ps__rail-y:focus,\n.material-style .ps-inverted .ps__rail-x.ps--clicking,\n.material-style .ps-inverted .ps__rail-y.ps--clicking {\n  background-color: rgba(255, 255, 255, 0.5);\n}\n.material-style .ps-inverted .ps__thumb-x,\n.material-style .ps-inverted .ps__thumb-y {\n  background-color: rgba(255, 255, 255, 0.7);\n}\n.material-style .ps-inverted .ps__rail-x:hover > .ps__thumb-x,\n.material-style .ps-inverted .ps__rail-y:hover > .ps__thumb-y,\n.material-style .ps-inverted .ps__rail-x:focus > .ps__thumb-x,\n.material-style .ps-inverted .ps__rail-y:focus > .ps__thumb-y,\n.material-style .ps-inverted .ps__rail-x.ps--clicking > .ps__thumb-x,\n.material-style .ps-inverted .ps__rail-y.ps--clicking > .ps__thumb-y {\n  background-color: #fff;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/lib/loader.js?!./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--12-2!./node_modules/sass-loader/lib/loader.js??ref--12-3!./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--12-2!../../../../../../node_modules/sass-loader/lib/loader.js??ref--12-3!./perfect-scrollbar.scss?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/lib/loader.js?!./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutFooter.vue?vue&type=template&id=3fe3a94b&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/LayoutFooter.vue?vue&type=template&id=3fe3a94b& ***!
  \*******************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "nav",
    { staticClass: "layout-footer footer", class: _vm.getLayoutFooterBg() },
    [_vm._m(0)]
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "container-fluid container-p-x pb-3" }, [
      _c("a", { staticClass: "footer-link pt-3", attrs: { href: "#" } }, [
        _vm._v("Link 1")
      ]),
      _vm._v(" "),
      _c("a", { staticClass: "footer-link pt-3 ml-4", attrs: { href: "#" } }, [
        _vm._v("Link 2")
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutNavbar.vue?vue&type=template&id=42d46580&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/LayoutNavbar.vue?vue&type=template&id=42d46580& ***!
  \*******************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "b-navbar",
    {
      staticClass: "layout-navbar align-items-lg-center container-p-x",
      attrs: { toggleable: "lg", variant: _vm.getLayoutNavbarBg() }
    },
    [
      _c("b-navbar-brand", { attrs: { to: "/" } }, [
        _vm._v("Laravel + Vue.js Starter")
      ]),
      _vm._v(" "),
      _vm.sidenavToggle
        ? _c(
            "b-navbar-nav",
            { staticClass: "align-items-lg-center mr-auto mr-lg-4" },
            [
              _c(
                "a",
                {
                  staticClass: "nav-item nav-link px-0 ml-2 ml-lg-0",
                  attrs: { href: "javascript:void(0)" },
                  on: { click: _vm.toggleSidenav }
                },
                [
                  _c("i", {
                    staticClass: "ion ion-md-menu text-large align-middle"
                  })
                ]
              )
            ]
          )
        : _vm._e(),
      _vm._v(" "),
      _c("b-navbar-toggle", { attrs: { target: "app-layout-navbar" } }),
      _vm._v(" "),
      _c(
        "b-collapse",
        { attrs: { "is-nav": "", id: "app-layout-navbar" } },
        [
          _c(
            "b-navbar-nav",
            { staticClass: "align-items-lg-center" },
            [
              _c("b-nav-item", { attrs: { href: "#" } }, [_vm._v("Link 1")]),
              _vm._v(" "),
              _c("b-nav-item", { attrs: { href: "#" } }, [_vm._v("Link 2")])
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutSidenav.vue?vue&type=template&id=3f81bd28&":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/LayoutSidenav.vue?vue&type=template&id=3f81bd28& ***!
  \********************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "sidenav",
    { class: _vm.curClasses, attrs: { orientation: _vm.orientation } },
    [
      _c(
        "div",
        {
          staticClass: "sidenav-inner",
          class: { "py-1": _vm.orientation !== "horizontal" }
        },
        [
          _c(
            "sidenav-router-link",
            { attrs: { icon: "ion ion-ios-contact", to: "/", exact: true } },
            [_vm._v("Home")]
          ),
          _vm._v(" "),
          _c(
            "sidenav-router-link",
            {
              attrs: { icon: "ion ion-md-desktop", to: "/page-2", exact: true }
            },
            [_vm._v("Page 2")]
          )
        ],
        1
      )
    ]
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=template&id=781fc20d&":
/*!********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=template&id=781fc20d& ***!
  \********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "sidenav-block" }, [_vm._t("default")], 2)
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=template&id=048b2306&":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=template&id=048b2306& ***!
  \************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "nav",
    { staticClass: "sidenav", class: "sidenav-" + _vm.orientation },
    [_vm._t("default")],
    2
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=template&id=b6fd9ece&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=template&id=b6fd9ece& ***!
  \**********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "sidenav-divider" })
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=template&id=90af4b86&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=template&id=90af4b86& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "sidenav-header" }, [_vm._t("default")], 2)
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=template&id=2847af2a&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=template&id=2847af2a& ***!
  \*******************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "sidenav-item",
      class: { active: _vm.active, disabled: _vm.disabled }
    },
    [
      _c(
        "a",
        {
          staticClass: "sidenav-link",
          class: _vm.linkClass,
          attrs: { href: _vm.href, target: _vm.target }
        },
        [
          _vm.icon
            ? _c("i", { staticClass: "sidenav-icon", class: _vm.icon })
            : _vm._e(),
          _vm._v(" "),
          _c("div", [_vm._t("default")], 2),
          _vm._v(" "),
          _vm.badge
            ? _c("div", { staticClass: "pl-1 ml-auto" }, [
                _c("div", { staticClass: "badge", class: _vm.badgeClass }, [
                  _vm._v(_vm._s(_vm.badge))
                ])
              ])
            : _vm._e()
        ]
      )
    ]
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=template&id=85c64be2&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=template&id=85c64be2& ***!
  \*******************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "sidenav-item",
      class: { active: _vm.active, disabled: _vm.disabled, open: _vm.open }
    },
    [
      _c(
        "a",
        {
          staticClass: "sidenav-link sidenav-toggle",
          class: _vm.linkClass,
          attrs: { href: "javascript:void(0)" }
        },
        [
          _vm.icon
            ? _c("i", { staticClass: "sidenav-icon", class: _vm.icon })
            : _vm._e(),
          _vm._v(" "),
          _c("div", [_vm._t("link-text")], 2),
          _vm._v(" "),
          _vm.badge
            ? _c("div", { staticClass: "pl-1 ml-auto" }, [
                _c("div", { staticClass: "badge", class: _vm.badgeClass }, [
                  _vm._v(_vm._s(_vm.badge))
                ])
              ])
            : _vm._e()
        ]
      ),
      _vm._v(" "),
      _c("div", { staticClass: "sidenav-menu" }, [_vm._t("default")], 2)
    ]
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=template&id=1866bf93&":
/*!*************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=template&id=1866bf93& ***!
  \*************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "router-link",
    {
      staticClass: "sidenav-item",
      class: { active: _vm.active, disabled: _vm.disabled },
      attrs: {
        tag: "div",
        "active-class": "active",
        to: _vm.to,
        replace: _vm.replace,
        append: _vm.append,
        exact: _vm.exact,
        event: _vm.event
      }
    },
    [
      _c("a", { staticClass: "sidenav-link", class: _vm.linkClass }, [
        _vm.icon
          ? _c("i", { staticClass: "sidenav-icon", class: _vm.icon })
          : _vm._e(),
        _vm._v(" "),
        _c("div", [_vm._t("default")], 2),
        _vm._v(" "),
        _vm.badge
          ? _c("div", { staticClass: "pl-1 ml-auto" }, [
              _c("div", { staticClass: "badge", class: _vm.badgeClass }, [
                _vm._v(_vm._s(_vm.badge))
              ])
            ])
          : _vm._e()
      ])
    ]
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/assets/src/layout/LayoutFooter.vue":
/*!******************************************************!*\
  !*** ./resources/assets/src/layout/LayoutFooter.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutFooter_vue_vue_type_template_id_3fe3a94b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutFooter.vue?vue&type=template&id=3fe3a94b& */ "./resources/assets/src/layout/LayoutFooter.vue?vue&type=template&id=3fe3a94b&");
/* harmony import */ var _LayoutFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutFooter.vue?vue&type=script&lang=js& */ "./resources/assets/src/layout/LayoutFooter.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _LayoutFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _LayoutFooter_vue_vue_type_template_id_3fe3a94b___WEBPACK_IMPORTED_MODULE_0__["render"],
  _LayoutFooter_vue_vue_type_template_id_3fe3a94b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/layout/LayoutFooter.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/layout/LayoutFooter.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/assets/src/layout/LayoutFooter.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutFooter.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutFooter.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/layout/LayoutFooter.vue?vue&type=template&id=3fe3a94b&":
/*!*************************************************************************************!*\
  !*** ./resources/assets/src/layout/LayoutFooter.vue?vue&type=template&id=3fe3a94b& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutFooter_vue_vue_type_template_id_3fe3a94b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutFooter.vue?vue&type=template&id=3fe3a94b& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutFooter.vue?vue&type=template&id=3fe3a94b&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutFooter_vue_vue_type_template_id_3fe3a94b___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutFooter_vue_vue_type_template_id_3fe3a94b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/layout/LayoutNavbar.vue":
/*!******************************************************!*\
  !*** ./resources/assets/src/layout/LayoutNavbar.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutNavbar_vue_vue_type_template_id_42d46580___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutNavbar.vue?vue&type=template&id=42d46580& */ "./resources/assets/src/layout/LayoutNavbar.vue?vue&type=template&id=42d46580&");
/* harmony import */ var _LayoutNavbar_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutNavbar.vue?vue&type=script&lang=js& */ "./resources/assets/src/layout/LayoutNavbar.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _LayoutNavbar_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _LayoutNavbar_vue_vue_type_template_id_42d46580___WEBPACK_IMPORTED_MODULE_0__["render"],
  _LayoutNavbar_vue_vue_type_template_id_42d46580___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/layout/LayoutNavbar.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/layout/LayoutNavbar.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/assets/src/layout/LayoutNavbar.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutNavbar_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutNavbar.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutNavbar.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutNavbar_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/layout/LayoutNavbar.vue?vue&type=template&id=42d46580&":
/*!*************************************************************************************!*\
  !*** ./resources/assets/src/layout/LayoutNavbar.vue?vue&type=template&id=42d46580& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutNavbar_vue_vue_type_template_id_42d46580___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutNavbar.vue?vue&type=template&id=42d46580& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutNavbar.vue?vue&type=template&id=42d46580&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutNavbar_vue_vue_type_template_id_42d46580___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutNavbar_vue_vue_type_template_id_42d46580___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/layout/LayoutSidenav.vue":
/*!*******************************************************!*\
  !*** ./resources/assets/src/layout/LayoutSidenav.vue ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutSidenav_vue_vue_type_template_id_3f81bd28___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutSidenav.vue?vue&type=template&id=3f81bd28& */ "./resources/assets/src/layout/LayoutSidenav.vue?vue&type=template&id=3f81bd28&");
/* harmony import */ var _LayoutSidenav_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutSidenav.vue?vue&type=script&lang=js& */ "./resources/assets/src/layout/LayoutSidenav.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _LayoutSidenav_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _LayoutSidenav_vue_vue_type_template_id_3f81bd28___WEBPACK_IMPORTED_MODULE_0__["render"],
  _LayoutSidenav_vue_vue_type_template_id_3f81bd28___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/layout/LayoutSidenav.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/layout/LayoutSidenav.vue?vue&type=script&lang=js&":
/*!********************************************************************************!*\
  !*** ./resources/assets/src/layout/LayoutSidenav.vue?vue&type=script&lang=js& ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutSidenav_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutSidenav.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutSidenav.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutSidenav_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/layout/LayoutSidenav.vue?vue&type=template&id=3f81bd28&":
/*!**************************************************************************************!*\
  !*** ./resources/assets/src/layout/LayoutSidenav.vue?vue&type=template&id=3f81bd28& ***!
  \**************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutSidenav_vue_vue_type_template_id_3f81bd28___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutSidenav.vue?vue&type=template&id=3f81bd28& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/LayoutSidenav.vue?vue&type=template&id=3f81bd28&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutSidenav_vue_vue_type_template_id_3f81bd28___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutSidenav_vue_vue_type_template_id_3f81bd28___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_12_2_node_modules_sass_loader_lib_loader_js_ref_12_3_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--12-2!../../../../../../node_modules/sass-loader/lib/loader.js??ref--12-3!./perfect-scrollbar.scss?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/lib/loader.js?!./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_12_2_node_modules_sass_loader_lib_loader_js_ref_12_3_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_12_2_node_modules_sass_loader_lib_loader_js_ref_12_3_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_12_2_node_modules_sass_loader_lib_loader_js_ref_12_3_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_12_2_node_modules_sass_loader_lib_loader_js_ref_12_3_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_12_2_node_modules_sass_loader_lib_loader_js_ref_12_3_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue":
/*!*******************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavBlock_vue_vue_type_template_id_781fc20d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavBlock.vue?vue&type=template&id=781fc20d& */ "./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=template&id=781fc20d&");
/* harmony import */ var _SidenavBlock_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavBlock.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SidenavBlock_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavBlock_vue_vue_type_template_id_781fc20d___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavBlock_vue_vue_type_template_id_781fc20d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavBlock_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavBlock.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavBlock_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=template&id=781fc20d&":
/*!**************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=template&id=781fc20d& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavBlock_vue_vue_type_template_id_781fc20d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavBlock.vue?vue&type=template&id=781fc20d& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue?vue&type=template&id=781fc20d&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavBlock_vue_vue_type_template_id_781fc20d___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavBlock_vue_vue_type_template_id_781fc20d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue":
/*!***********************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavComponent_vue_vue_type_template_id_048b2306___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavComponent.vue?vue&type=template&id=048b2306& */ "./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=template&id=048b2306&");
/* harmony import */ var _SidenavComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavComponent.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _vendor_libs_perfect_scrollbar_perfect_scrollbar_scss_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss& */ "./resources/assets/src/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _SidenavComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavComponent_vue_vue_type_template_id_048b2306___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavComponent_vue_vue_type_template_id_048b2306___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=script&lang=js&":
/*!************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=template&id=048b2306&":
/*!******************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=template&id=048b2306& ***!
  \******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavComponent_vue_vue_type_template_id_048b2306___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavComponent.vue?vue&type=template&id=048b2306& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue?vue&type=template&id=048b2306&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavComponent_vue_vue_type_template_id_048b2306___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavComponent_vue_vue_type_template_id_048b2306___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue":
/*!*********************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavDivider_vue_vue_type_template_id_b6fd9ece___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavDivider.vue?vue&type=template&id=b6fd9ece& */ "./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=template&id=b6fd9ece&");
/* harmony import */ var _SidenavDivider_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavDivider.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SidenavDivider_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavDivider_vue_vue_type_template_id_b6fd9ece___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavDivider_vue_vue_type_template_id_b6fd9ece___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavDivider_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavDivider.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavDivider_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=template&id=b6fd9ece&":
/*!****************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=template&id=b6fd9ece& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavDivider_vue_vue_type_template_id_b6fd9ece___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavDivider.vue?vue&type=template&id=b6fd9ece& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue?vue&type=template&id=b6fd9ece&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavDivider_vue_vue_type_template_id_b6fd9ece___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavDivider_vue_vue_type_template_id_b6fd9ece___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue":
/*!********************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavHeader_vue_vue_type_template_id_90af4b86___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavHeader.vue?vue&type=template&id=90af4b86& */ "./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=template&id=90af4b86&");
/* harmony import */ var _SidenavHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavHeader.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SidenavHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavHeader_vue_vue_type_template_id_90af4b86___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavHeader_vue_vue_type_template_id_90af4b86___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavHeader.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavHeader_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=template&id=90af4b86&":
/*!***************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=template&id=90af4b86& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavHeader_vue_vue_type_template_id_90af4b86___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavHeader.vue?vue&type=template&id=90af4b86& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue?vue&type=template&id=90af4b86&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavHeader_vue_vue_type_template_id_90af4b86___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavHeader_vue_vue_type_template_id_90af4b86___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue":
/*!******************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavLink_vue_vue_type_template_id_2847af2a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavLink.vue?vue&type=template&id=2847af2a& */ "./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=template&id=2847af2a&");
/* harmony import */ var _SidenavLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavLink.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SidenavLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavLink_vue_vue_type_template_id_2847af2a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavLink_vue_vue_type_template_id_2847af2a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavLink.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavLink.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=template&id=2847af2a&":
/*!*************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=template&id=2847af2a& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavLink_vue_vue_type_template_id_2847af2a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavLink.vue?vue&type=template&id=2847af2a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue?vue&type=template&id=2847af2a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavLink_vue_vue_type_template_id_2847af2a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavLink_vue_vue_type_template_id_2847af2a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue":
/*!******************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavMenu_vue_vue_type_template_id_85c64be2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavMenu.vue?vue&type=template&id=85c64be2& */ "./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=template&id=85c64be2&");
/* harmony import */ var _SidenavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavMenu.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SidenavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavMenu_vue_vue_type_template_id_85c64be2___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavMenu_vue_vue_type_template_id_85c64be2___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavMenu.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=template&id=85c64be2&":
/*!*************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=template&id=85c64be2& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavMenu_vue_vue_type_template_id_85c64be2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavMenu.vue?vue&type=template&id=85c64be2& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue?vue&type=template&id=85c64be2&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavMenu_vue_vue_type_template_id_85c64be2___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavMenu_vue_vue_type_template_id_85c64be2___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue":
/*!************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavRouterLink_vue_vue_type_template_id_1866bf93___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavRouterLink.vue?vue&type=template&id=1866bf93& */ "./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=template&id=1866bf93&");
/* harmony import */ var _SidenavRouterLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavRouterLink.vue?vue&type=script&lang=js& */ "./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SidenavRouterLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SidenavRouterLink_vue_vue_type_template_id_1866bf93___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SidenavRouterLink_vue_vue_type_template_id_1866bf93___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavRouterLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavRouterLink.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavRouterLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=template&id=1866bf93&":
/*!*******************************************************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=template&id=1866bf93& ***!
  \*******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavRouterLink_vue_vue_type_template_id_1866bf93___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidenavRouterLink.vue?vue&type=template&id=1866bf93& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue?vue&type=template&id=1866bf93&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavRouterLink_vue_vue_type_template_id_1866bf93___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SidenavRouterLink_vue_vue_type_template_id_1866bf93___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/index.js":
/*!***********************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/index.js ***!
  \***********************************************************/
/*! exports provided: Sidenav, SidenavLink, SidenavRouterLink, SidenavMenu, SidenavHeader, SidenavBlock, SidenavDivider */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SidenavComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidenavComponent */ "./resources/assets/src/vendor/libs/sidenav/SidenavComponent.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "Sidenav", function() { return _SidenavComponent__WEBPACK_IMPORTED_MODULE_0__["default"]; });

/* harmony import */ var _SidenavLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidenavLink */ "./resources/assets/src/vendor/libs/sidenav/SidenavLink.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SidenavLink", function() { return _SidenavLink__WEBPACK_IMPORTED_MODULE_1__["default"]; });

/* harmony import */ var _SidenavRouterLink__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SidenavRouterLink */ "./resources/assets/src/vendor/libs/sidenav/SidenavRouterLink.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SidenavRouterLink", function() { return _SidenavRouterLink__WEBPACK_IMPORTED_MODULE_2__["default"]; });

/* harmony import */ var _SidenavMenu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SidenavMenu */ "./resources/assets/src/vendor/libs/sidenav/SidenavMenu.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SidenavMenu", function() { return _SidenavMenu__WEBPACK_IMPORTED_MODULE_3__["default"]; });

/* harmony import */ var _SidenavHeader__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SidenavHeader */ "./resources/assets/src/vendor/libs/sidenav/SidenavHeader.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SidenavHeader", function() { return _SidenavHeader__WEBPACK_IMPORTED_MODULE_4__["default"]; });

/* harmony import */ var _SidenavBlock__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./SidenavBlock */ "./resources/assets/src/vendor/libs/sidenav/SidenavBlock.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SidenavBlock", function() { return _SidenavBlock__WEBPACK_IMPORTED_MODULE_5__["default"]; });

/* harmony import */ var _SidenavDivider__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./SidenavDivider */ "./resources/assets/src/vendor/libs/sidenav/SidenavDivider.vue");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SidenavDivider", function() { return _SidenavDivider__WEBPACK_IMPORTED_MODULE_6__["default"]; });








/* eslint-disable vue/no-unused-components */


/* eslint-enable vue/no-unused-components */

/***/ }),

/***/ "./resources/assets/src/vendor/libs/sidenav/sidenav.js":
/*!*************************************************************!*\
  !*** ./resources/assets/src/vendor/libs/sidenav/sidenav.js ***!
  \*************************************************************/
/*! exports provided: SideNav */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SideNav", function() { return SideNav; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var TRANSITION_EVENTS = ['transitionend', 'webkitTransitionEnd', 'oTransitionEnd'];
var TRANSITION_PROPERTIES = ['transition', 'MozTransition', 'webkitTransition', 'WebkitTransition', 'OTransition'];
var DELTA = 5;

var SideNav =
/*#__PURE__*/
function () {
  function SideNav(el) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    var _PS = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

    _classCallCheck(this, SideNav);

    this._el = el;
    this._horizontal = options.orientation === 'horizontal';
    this._animate = options.animate !== false && this._supportsTransitionEnd();
    this._accordion = options.accordion !== false;
    this._closeChildren = Boolean(options.closeChildren);
    this._showDropdownOnHover = Boolean(options.showDropdownOnHover);
    this._rtl = document.documentElement.getAttribute('dir') === 'rtl' || document.body.getAttribute('dir') === 'rtl';
    this._lastWidth = this._horizontal ? window.innerWidth : null;

    this._onOpen = options.onOpen || function () {};

    this._onOpened = options.onOpened || function () {};

    this._onClose = options.onClose || function () {};

    this._onClosed = options.onClosed || function () {};

    el.classList.add('sidenav');
    el.classList[this._animate ? 'remove' : 'add']('sidenav-no-animation');

    if (!this._horizontal) {
      el.classList.add('sidenav-vertical');
      el.classList.remove('sidenav-horizontal');
      var PerfectScrollbarLib = _PS || window.PerfectScrollbar;

      if (PerfectScrollbarLib) {
        this._scrollbar = new PerfectScrollbarLib(el.querySelector('.sidenav-inner'), {
          suppressScrollX: true,
          wheelPropagation: true
        });
      }
    } else {
      el.classList.add('sidenav-horizontal');
      el.classList.remove('sidenav-vertical');
      this._inner = el.querySelector('.sidenav-inner');
      var container = this._inner.parentNode;
      this._prevBtn = el.querySelector('.sidenav-horizontal-prev');

      if (!this._prevBtn) {
        this._prevBtn = document.createElement('a');
        this._prevBtn.href = '#';
        this._prevBtn.className = 'sidenav-horizontal-prev';
        container.appendChild(this._prevBtn);
      }

      this._wrapper = el.querySelector('.sidenav-horizontal-wrapper');

      if (!this._wrapper) {
        this._wrapper = document.createElement('div');
        this._wrapper.className = 'sidenav-horizontal-wrapper';

        this._wrapper.appendChild(this._inner);

        container.appendChild(this._wrapper);
      }

      this._nextBtn = el.querySelector('.sidenav-horizontal-next');

      if (!this._nextBtn) {
        this._nextBtn = document.createElement('a');
        this._nextBtn.href = '#';
        this._nextBtn.className = 'sidenav-horizontal-next';
        container.appendChild(this._nextBtn);
      }

      this._innerPosition = 0;
      this.update();
    }

    this._bindEvents(); // Link sidenav instance to element


    el.sidenavInstance = this;
  }

  _createClass(SideNav, [{
    key: "open",
    value: function open(el) {
      var _this = this;

      var closeChildren = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this._closeChildren;

      var item = this._findUnopenedParent(this._getItem(el, true), closeChildren);

      if (!item) return;

      var toggleLink = this._getLink(item, true);

      if (!this._horizontal || !this._isRoot(item)) {
        if (this._animate) {
          this._onOpen(this, item, toggleLink, this._findMenu(item));

          window.requestAnimationFrame(function () {
            return _this._toggleAnimation(true, item, false);
          });
        } else {
          this._onOpen(this, item, toggleLink, this._findMenu(item));

          item.classList.add('open');

          this._onOpened(this, item, toggleLink, this._findMenu(item));
        }

        if (this._accordion) this._closeOther(item, closeChildren);
      } else {
        this._onOpen(this, item, toggleLink, this._findMenu(item));

        this._toggleDropdown(true, item, closeChildren);

        this._onOpened(this, item, toggleLink, this._findMenu(item));
      }
    }
  }, {
    key: "close",
    value: function close(el) {
      var _this2 = this;

      var closeChildren = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this._closeChildren;

      var item = this._getItem(el, true);

      var toggleLink = this._getLink(el, true);

      if (!item.classList.contains('open') || item.classList.contains('disabled')) return;

      if (!this._horizontal || !this._isRoot(item)) {
        if (this._animate) {
          this._onClose(this, item, toggleLink, this._findMenu(item));

          window.requestAnimationFrame(function () {
            return _this2._toggleAnimation(false, item, closeChildren);
          });
        } else {
          this._onClose(this, item, toggleLink, this._findMenu(item));

          item.classList.remove('open');

          if (closeChildren) {
            var opened = item.querySelectorAll('.sidenav-item.open');

            for (var i = 0, l = opened.length; i < l; i++) {
              opened[i].classList.remove('open');
            }
          }

          this._onClosed(this, item, toggleLink, this._findMenu(item));
        }
      } else {
        this._onClose(this, item, toggleLink, this._findMenu(item));

        this._toggleDropdown(false, item, closeChildren);

        this._onClosed(this, item, toggleLink, this._findMenu(item));
      }
    }
  }, {
    key: "toggle",
    value: function toggle(el) {
      var closeChildren = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this._closeChildren;

      var item = this._getItem(el, true);

      var toggleLink = this._getLink(el, true);

      if (item.classList.contains('open')) this.close(item, closeChildren);else this.open(item, closeChildren);
    }
  }, {
    key: "closeAll",
    value: function closeAll() {
      var closeChildren = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this._closeChildren;

      var opened = this._el.querySelectorAll('.sidenav-inner > .sidenav-item.open');

      for (var i = 0, l = opened.length; i < l; i++) {
        this.close(opened[i], closeChildren);
      }
    }
  }, {
    key: "setActive",
    value: function setActive(el, active) {
      var openTree = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var deactivateOthers = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;

      var item = this._getItem(el, false);

      if (active && deactivateOthers) {
        var activeItems = this._el.querySelectorAll('.sidenav-inner .sidenav-item.active');

        for (var i = 0, l = activeItems.length; i < l; i++) {
          activeItems[i].classList.remove('active');
        }
      }

      if (active && openTree) {
        var parentItem = this._findParent(item, 'sidenav-item', false);

        parentItem && this.open(parentItem);
      }

      while (item) {
        item.classList[active ? 'add' : 'remove']('active');
        item = this._findParent(item, 'sidenav-item', false);
      }
    }
  }, {
    key: "setDisabled",
    value: function setDisabled(el, disabled) {
      this._getItem(el, false).classList[disabled ? 'add' : 'remove']('disabled');
    }
  }, {
    key: "isActive",
    value: function isActive(el) {
      return this._getItem(el, false).classList.contains('active');
    }
  }, {
    key: "isOpened",
    value: function isOpened(el) {
      return this._getItem(el, false).classList.contains('open');
    }
  }, {
    key: "isDisabled",
    value: function isDisabled(el) {
      return this._getItem(el, false).classList.contains('disabled');
    }
  }, {
    key: "update",
    value: function update() {
      if (!this._horizontal) {
        if (this._scrollbar) {
          this._scrollbar.update();
        }
      } else {
        this.closeAll();
        var wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width);
        var innerWidth = this._innerWidth;
        var position = this._innerPosition;

        if (wrapperWidth - position > innerWidth) {
          position = wrapperWidth - innerWidth;
          if (position > 0) position = 0;
          this._innerPosition = position;
        }

        this._updateSlider(wrapperWidth, innerWidth, position);
      }
    }
  }, {
    key: "_updateSlider",
    value: function _updateSlider() {
      var wrapperWidth = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var innerWidth = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var position = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

      var _wrapperWidth = wrapperWidth !== null ? wrapperWidth : Math.round(this._wrapper.getBoundingClientRect().width);

      var _innerWidth = innerWidth !== null ? innerWidth : this._innerWidth;

      var _position = position !== null ? position : this._innerPosition;

      if (_position === 0) this._prevBtn.classList.add('disabled');else this._prevBtn.classList.remove('disabled');
      if (_innerWidth + _position <= _wrapperWidth) this._nextBtn.classList.add('disabled');else this._nextBtn.classList.remove('disabled');
    }
  }, {
    key: "destroy",
    value: function destroy() {
      if (!this._el) return;

      this._unbindEvents();

      var items = this._el.querySelectorAll('.sidenav-item');

      for (var i = 0, l = items.length; i < l; i++) {
        this._unbindAnimationEndEvent(items[i]);

        items[i].classList.remove('sidenav-item-animating');
        items[i].classList.remove('open');
        items[i].style.overflow = null;
        items[i].style.height = null;
      }

      var menus = this._el.querySelectorAll('.sidenav-menu');

      for (var i2 = 0, l2 = menus.length; i2 < l2; i2++) {
        menus[i2].style.marginRight = null;
        menus[i2].style.marginLeft = null;
      }

      this._el.classList.remove('sidenav-no-animation');

      if (this._wrapper) {
        this._prevBtn.parentNode.removeChild(this._prevBtn);

        this._nextBtn.parentNode.removeChild(this._nextBtn);

        this._wrapper.parentNode.insertBefore(this._inner, this._wrapper);

        this._wrapper.parentNode.removeChild(this._wrapper);

        this._inner.style.marginLeft = null;
        this._inner.style.marginRight = null;
      }

      this._el.sidenavInstance = null;
      delete this._el.sidenavInstance;
      this._el = null;
      this._horizontal = null;
      this._animate = null;
      this._accordion = null;
      this._closeChildren = null;
      this._showDropdownOnHover = null;
      this._rtl = null;
      this._onOpen = null;
      this._onOpened = null;
      this._onClose = null;
      this._onClosed = null;

      if (this._scrollbar) {
        this._scrollbar.destroy();

        this._scrollbar = null;
      }

      this._inner = null;
      this._prevBtn = null;
      this._wrapper = null;
      this._nextBtn = null;
    }
  }, {
    key: "_getLink",
    value: function _getLink(el, toggle) {
      var found = [];
      var selector = toggle ? 'sidenav-toggle' : 'sidenav-link';
      if (el.classList.contains(selector)) found = [el];else if (el.classList.contains('sidenav-item')) found = this._findChild(el, [selector]);
      if (!found.length) throw new Error("`".concat(selector, "` element not found."));
      return found[0];
    }
  }, {
    key: "_getItem",
    value: function _getItem(el, toggle) {
      var item = null;
      var selector = toggle ? 'sidenav-toggle' : 'sidenav-link';

      if (el.classList.contains('sidenav-item')) {
        if (this._findChild(el, [selector]).length) item = el;
      } else if (el.classList.contains(selector)) {
        item = el.parentNode.classList.contains('sidenav-item') ? el.parentNode : null;
      }

      if (!item) {
        throw new Error("".concat(toggle ? 'Toggable ' : '', "`.sidenav-item` element not found."));
      }

      return item;
    }
  }, {
    key: "_findUnopenedParent",
    value: function _findUnopenedParent(item, closeChildren) {
      var tree = [];
      var parentItem = null;

      while (item) {
        if (item.classList.contains('disabled')) {
          parentItem = null;
          tree = [];
        } else {
          if (!item.classList.contains('open')) parentItem = item;
          tree.push(item);
        }

        item = this._findParent(item, 'sidenav-item', false);
      }

      if (!parentItem) return null;
      if (tree.length === 1) return parentItem;
      tree = tree.slice(0, tree.indexOf(parentItem));

      for (var i = 0, l = tree.length; i < l; i++) {
        tree[i].classList.add('open');

        if (this._accordion) {
          var openedItems = this._findChild(tree[i].parentNode, ['sidenav-item', 'open']);

          for (var j = 0, k = openedItems.length; j < k; j++) {
            if (openedItems[j] === tree[i]) continue;
            openedItems[j].classList.remove('open');

            if (closeChildren) {
              var openedChildren = openedItems[j].querySelectorAll('.sidenav-item.open');

              for (var x = 0, z = openedChildren.length; x < z; x++) {
                openedChildren[x].classList.remove('open');
              }
            }
          }
        }
      }

      return parentItem;
    }
  }, {
    key: "_closeOther",
    value: function _closeOther(item, closeChildren) {
      var opened = this._findChild(item.parentNode, ['sidenav-item', 'open']);

      for (var i = 0, l = opened.length; i < l; i++) {
        if (opened[i] !== item) this.close(opened[i], closeChildren);
      }
    }
  }, {
    key: "_toggleAnimation",
    value: function _toggleAnimation(open, item, closeChildren) {
      var _this3 = this;

      var toggleLink = this._getLink(item, true);

      var menu = this._findMenu(item);

      this._unbindAnimationEndEvent(item);

      var linkHeight = Math.round(toggleLink.getBoundingClientRect().height);
      item.style.overflow = 'hidden';

      var clearItemStyle = function clearItemStyle() {
        item.classList.remove('sidenav-item-animating');
        item.classList.remove('sidenav-item-closing');
        item.style.overflow = null;
        item.style.height = null;
        if (!_this3._horizontal) _this3.update();
      };

      if (open) {
        item.style.height = "".concat(linkHeight, "px");
        item.classList.add('sidenav-item-animating');
        item.classList.add('open');

        this._bindAnimationEndEvent(item, function () {
          clearItemStyle();

          _this3._onOpened(_this3, item, toggleLink, menu);
        });

        setTimeout(function () {
          return item.style.height = "".concat(linkHeight + Math.round(menu.getBoundingClientRect().height), "px");
        }, 50);
      } else {
        item.style.height = "".concat(linkHeight + Math.round(menu.getBoundingClientRect().height), "px");
        item.classList.add('sidenav-item-animating');
        item.classList.add('sidenav-item-closing');

        this._bindAnimationEndEvent(item, function () {
          item.classList.remove('open');
          clearItemStyle();

          if (closeChildren) {
            var opened = item.querySelectorAll('.sidenav-item.open');

            for (var i = 0, l = opened.length; i < l; i++) {
              opened[i].classList.remove('open');
            }
          }

          _this3._onClosed(_this3, item, toggleLink, menu);
        });

        setTimeout(function () {
          return item.style.height = "".concat(linkHeight, "px");
        }, 50);
      }
    }
  }, {
    key: "_toggleDropdown",
    value: function _toggleDropdown(show, item, closeChildren) {
      var menu = this._findMenu(item);

      if (show) {
        var wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width);
        var innerWidth = this._innerWidth;
        var position = this._innerPosition;

        var itemOffset = this._getItemOffset(item);

        var itemWidth = Math.round(item.getBoundingClientRect().width);

        if (itemOffset - DELTA <= -1 * position) {
          this._innerPosition = -1 * itemOffset;
        } else if (itemOffset + position + itemWidth + DELTA >= wrapperWidth) {
          if (itemWidth > wrapperWidth) {
            this._innerPosition = -1 * itemOffset;
          } else {
            this._innerPosition = -1 * (itemOffset + itemWidth - wrapperWidth);
          }
        }

        item.classList.add('open');
        var menuWidth = Math.round(menu.getBoundingClientRect().width);

        if (itemOffset + this._innerPosition + menuWidth > wrapperWidth && menuWidth < wrapperWidth && menuWidth > itemWidth) {
          menu.style[this._rtl ? 'marginRight' : 'marginLeft'] = "-".concat(menuWidth - itemWidth, "px");
        }

        this._closeOther(item, closeChildren);

        this._updateSlider();
      } else {
        var toggle = this._findChild(item, ['sidenav-toggle']);

        toggle.length && toggle[0].removeAttribute('data-hover', 'true');
        item.classList.remove('open');
        menu.style[this._rtl ? 'marginRight' : 'marginLeft'] = null;

        if (closeChildren) {
          var opened = menu.querySelectorAll('.sidenav-item.open');

          for (var i = 0, l = opened.length; i < l; i++) {
            opened[i].classList.remove('open');
          }
        }
      }
    }
  }, {
    key: "_slide",
    value: function _slide(direction) {
      var wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width);
      var innerWidth = this._innerWidth;
      var position = this._innerPosition;
      var newPosition;

      if (direction === 'next') {
        newPosition = this._getSlideNextPos();

        if (innerWidth + newPosition < wrapperWidth) {
          newPosition = wrapperWidth - innerWidth;
        }
      } else {
        newPosition = this._getSlidePrevPos();
        if (newPosition > 0) newPosition = 0;
      }

      this._innerPosition = newPosition;
      this.update();
    }
  }, {
    key: "_getSlideNextPos",
    value: function _getSlideNextPos() {
      var wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width);
      var position = this._innerPosition;
      var curItem = this._inner.childNodes[0];
      var left = 0;

      while (curItem) {
        if (curItem.tagName) {
          var curItemWidth = Math.round(curItem.getBoundingClientRect().width);

          if (left + position - DELTA <= wrapperWidth && left + position + curItemWidth + DELTA >= wrapperWidth) {
            if (curItemWidth > wrapperWidth && left === -1 * position) left += curItemWidth;
            break;
          }

          left += curItemWidth;
        }

        curItem = curItem.nextSibling;
      }

      return -1 * left;
    }
  }, {
    key: "_getSlidePrevPos",
    value: function _getSlidePrevPos() {
      var wrapperWidth = Math.round(this._wrapper.getBoundingClientRect().width);
      var position = this._innerPosition;
      var curItem = this._inner.childNodes[0];
      var left = 0;

      while (curItem) {
        if (curItem.tagName) {
          var curItemWidth = Math.round(curItem.getBoundingClientRect().width);

          if (left - DELTA <= -1 * position && left + curItemWidth + DELTA >= -1 * position) {
            if (curItemWidth <= wrapperWidth) left = left + curItemWidth - wrapperWidth;
            break;
          }

          left += curItemWidth;
        }

        curItem = curItem.nextSibling;
      }

      return -1 * left;
    }
  }, {
    key: "_getItemOffset",
    value: function _getItemOffset(item) {
      var curItem = this._inner.childNodes[0];
      var left = 0;

      while (curItem !== item) {
        if (curItem.tagName) {
          left += Math.round(curItem.getBoundingClientRect().width);
        }

        curItem = curItem.nextSibling;
      }

      return left;
    }
  }, {
    key: "_bindAnimationEndEvent",
    value: function _bindAnimationEndEvent(el, handler) {
      var _this4 = this;

      var cb = function cb(e) {
        if (e.target !== el) return;

        _this4._unbindAnimationEndEvent(el);

        handler(e);
      };

      var duration = window.getComputedStyle(el).transitionDuration;
      duration = parseFloat(duration) * (duration.indexOf('ms') !== -1 ? 1 : 1000);
      el._sideNavAnimationEndEventCb = cb;
      TRANSITION_EVENTS.forEach(function (ev) {
        return el.addEventListener(ev, el._sideNavAnimationEndEventCb, false);
      });
      el._sideNavAnimationEndEventTimeout = setTimeout(function () {
        cb({
          target: el
        });
      }, duration + 50);
    }
  }, {
    key: "_unbindAnimationEndEvent",
    value: function _unbindAnimationEndEvent(el) {
      var cb = el._sideNavAnimationEndEventCb;

      if (el._sideNavAnimationEndEventTimeout) {
        clearTimeout(el._sideNavAnimationEndEventTimeout);
        el._sideNavAnimationEndEventTimeout = null;
      }

      if (!cb) return;
      TRANSITION_EVENTS.forEach(function (ev) {
        return el.removeEventListener(ev, cb, false);
      });
      el._sideNavAnimationEndEventCb = null;
    }
  }, {
    key: "_bindEvents",
    value: function _bindEvents() {
      var _this5 = this;

      this._evntElClick = function (e) {
        var toggleLink = e.target.classList.contains('sidenav-toggle') ? e.target : _this5._findParent(e.target, 'sidenav-toggle', false);

        if (toggleLink) {
          e.preventDefault();

          if (toggleLink.getAttribute('data-hover') !== 'true') {
            _this5.toggle(toggleLink);
          }
        }
      };

      this._el.addEventListener('click', this._evntElClick);

      this._evntWindowResize = function () {
        if (!_this5._horizontal) {
          _this5.update();
        } else if (_this5._lastWidth !== window.innerWidth) {
          _this5._lastWidth = window.innerWidth;

          _this5.update();
        }
      };

      window.addEventListener('resize', this._evntWindowResize);

      if (this._horizontal) {
        this._evntPrevBtnClick = function (e) {
          e.preventDefault();
          if (_this5._prevBtn.classList.contains('disabled')) return;

          _this5._slide('prev');
        };

        this._prevBtn.addEventListener('click', this._evntPrevBtnClick);

        this._evntNextBtnClick = function (e) {
          e.preventDefault();
          if (_this5._nextBtn.classList.contains('disabled')) return;

          _this5._slide('next');
        };

        this._nextBtn.addEventListener('click', this._evntNextBtnClick);

        this._evntBodyClick = function (e) {
          if (!_this5._inner.contains(e.target) && _this5._el.querySelectorAll('.sidenav-inner > .sidenav-item.open').length) _this5.closeAll();
        };

        document.body.addEventListener('click', this._evntBodyClick);

        this._evntHorizontalElClick = function (e) {
          var link = e.target.classList.contains('sidenav-link') ? e.target : _this5._findParent(e.target, 'sidenav-link', false);
          if (link && !link.classList.contains('sidenav-toggle')) _this5.closeAll();
        };

        this._el.addEventListener('click', this._evntHorizontalElClick);

        if (this._showDropdownOnHover) {
          this._evntInnerMousemove = function (e) {
            var curItem = _this5._findParent(e.target, 'sidenav-item', false);

            var item = null;

            while (curItem) {
              item = curItem;
              curItem = _this5._findParent(curItem, 'sidenav-item', false);
            }

            if (item && !item.classList.contains('open')) {
              var toggle = _this5._findChild(item, ['sidenav-toggle']);

              if (toggle.length) {
                toggle[0].setAttribute('data-hover', 'true');

                _this5.open(toggle[0], _this5._closeChildren, true);

                setTimeout(function () {
                  toggle[0].removeAttribute('data-hover');
                }, 500);
              }
            }
          };

          this._inner.addEventListener('mousemove', this._evntInnerMousemove);

          this._evntInnerMouseleave = function (e) {
            _this5.closeAll();
          };

          this._inner.addEventListener('mouseleave', this._evntInnerMouseleave);
        }
      }
    }
  }, {
    key: "_unbindEvents",
    value: function _unbindEvents() {
      if (this._evntElClick) {
        this._el.removeEventListener('click', this._evntElClick);

        this._evntElClick = null;
      }

      if (this._evntWindowResize) {
        window.removeEventListener('resize', this._evntWindowResize);
        this._evntWindowResize = null;
      }

      if (this._evntPrevBtnClick) {
        this._prevBtn.removeEventListener('click', this._evntPrevBtnClick);

        this._evntPrevBtnClick = null;
      }

      if (this._evntNextBtnClick) {
        this._nextBtn.removeEventListener('click', this._evntNextBtnClick);

        this._evntNextBtnClick = null;
      }

      if (this._evntBodyClick) {
        document.body.removeEventListener('click', this._evntBodyClick);
        this._evntBodyClick = null;
      }

      if (this._evntHorizontalElClick) {
        this._el.removeEventListener('click', this._evntHorizontalElClick);

        this._evntHorizontalElClick = null;
      }

      if (this._evntInnerMousemove) {
        this._inner.removeEventListener('mousemove', this._evntInnerMousemove);

        this._evntInnerMousemove = null;
      }

      if (this._evntInnerMouseleave) {
        this._inner.removeEventListener('mouseleave', this._evntInnerMouseleave);

        this._evntInnerMouseleave = null;
      }
    }
  }, {
    key: "_findMenu",
    value: function _findMenu(item) {
      var curEl = item.childNodes[0];
      var menu = null;

      while (curEl && !menu) {
        if (curEl.classList && curEl.classList.contains('sidenav-menu')) menu = curEl;
        curEl = curEl.nextSibling;
      }

      if (!menu) throw new Error('Cannot find `.sidenav-menu` element for the current `.sidenav-toggle`');
      return menu;
    }
  }, {
    key: "_isRoot",
    value: function _isRoot(item) {
      return !this._findParent(item, 'sidenav-item', false);
    }
  }, {
    key: "_findParent",
    value: function _findParent(el, cls) {
      var throwError = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      if (el.tagName.toUpperCase() === 'BODY') return null;
      el = el.parentNode;

      while (el.tagName.toUpperCase() !== 'BODY' && !el.classList.contains(cls)) {
        el = el.parentNode;
      }

      el = el.tagName.toUpperCase() !== 'BODY' ? el : null;
      if (!el && throwError) throw new Error("Cannot find `.".concat(cls, "` parent element"));
      return el;
    }
  }, {
    key: "_findChild",
    value: function _findChild(el, cls) {
      var items = el.childNodes;
      var found = [];

      for (var i = 0, l = items.length, link; i < l; i++) {
        if (items[i].classList) {
          var passed = 0;

          for (var j = 0; j < cls.length; j++) {
            if (items[i].classList.contains(cls[j])) passed++;
          }

          if (cls.length === passed) found.push(items[i]);
        }
      }

      return found;
    }
  }, {
    key: "_supportsTransitionEnd",
    value: function _supportsTransitionEnd() {
      if (window.QUnit) {
        return false;
      }

      var el = document.body || document.documentElement;
      var result = false;
      TRANSITION_PROPERTIES.forEach(function (evnt) {
        if (typeof el.style[evnt] !== 'undefined') result = true;
      });
      return result;
    }
  }, {
    key: "_innerWidth",
    get: function get() {
      var items = this._inner.childNodes;
      var width = 0;

      for (var i = 0, l = items.length; i < l; i++) {
        if (items[i].tagName) {
          width += Math.round(items[i].getBoundingClientRect().width);
        }
      }

      return width;
    }
  }, {
    key: "_innerPosition",
    get: function get() {
      return parseInt(this._inner.style[this._rtl ? 'marginRight' : 'marginLeft'] || '0px');
    },
    set: function set(value) {
      this._inner.style[this._rtl ? 'marginRight' : 'marginLeft'] = "".concat(value, "px");
      return value;
    }
  }]);

  return SideNav;
}();



/***/ })

}]);