(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[3],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/Layout2.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/Layout2.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutNavbar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutNavbar */ "./resources/assets/src/layout/LayoutNavbar.vue");
/* harmony import */ var _LayoutSidenav__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutSidenav */ "./resources/assets/src/layout/LayoutSidenav.vue");
/* harmony import */ var _LayoutFooter__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./LayoutFooter */ "./resources/assets/src/layout/LayoutFooter.vue");
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
  name: 'app-layout-2',
  components: {
    'app-layout-navbar': _LayoutNavbar__WEBPACK_IMPORTED_MODULE_0__["default"],
    'app-layout-sidenav': _LayoutSidenav__WEBPACK_IMPORTED_MODULE_1__["default"],
    'app-layout-footer': _LayoutFooter__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  mounted: function mounted() {
    this.layoutHelpers.init();
    this.layoutHelpers.update();
    this.layoutHelpers.setAutoUpdate(true);
  },
  beforeDestroy: function beforeDestroy() {
    this.layoutHelpers.destroy();
  },
  methods: {
    closeSidenav: function closeSidenav() {
      this.layoutHelpers.setCollapsed(true);
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/Layout2.vue?vue&type=template&id=600df452&":
/*!**************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/assets/src/layout/Layout2.vue?vue&type=template&id=600df452& ***!
  \**************************************************************************************************************************************************************************************************************/
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
  return _c("div", { staticClass: "layout-wrapper layout-2" }, [
    _c(
      "div",
      { staticClass: "layout-inner" },
      [
        _c("app-layout-sidenav"),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "layout-container" },
          [
            _c("app-layout-navbar"),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "layout-content" },
              [
                _c(
                  "div",
                  {
                    staticClass:
                      "router-transitions container-fluid flex-grow-1 container-p-y"
                  },
                  [_c("router-view")],
                  1
                ),
                _vm._v(" "),
                _c("app-layout-footer")
              ],
              1
            )
          ],
          1
        )
      ],
      1
    ),
    _vm._v(" "),
    _c("div", {
      staticClass: "layout-overlay",
      on: { click: _vm.closeSidenav }
    })
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/assets/src/layout/Layout2.vue":
/*!*************************************************!*\
  !*** ./resources/assets/src/layout/Layout2.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Layout2_vue_vue_type_template_id_600df452___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Layout2.vue?vue&type=template&id=600df452& */ "./resources/assets/src/layout/Layout2.vue?vue&type=template&id=600df452&");
/* harmony import */ var _Layout2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Layout2.vue?vue&type=script&lang=js& */ "./resources/assets/src/layout/Layout2.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Layout2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Layout2_vue_vue_type_template_id_600df452___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Layout2_vue_vue_type_template_id_600df452___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/assets/src/layout/Layout2.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/assets/src/layout/Layout2.vue?vue&type=script&lang=js&":
/*!**************************************************************************!*\
  !*** ./resources/assets/src/layout/Layout2.vue?vue&type=script&lang=js& ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Layout2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Layout2.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/Layout2.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Layout2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/assets/src/layout/Layout2.vue?vue&type=template&id=600df452&":
/*!********************************************************************************!*\
  !*** ./resources/assets/src/layout/Layout2.vue?vue&type=template&id=600df452& ***!
  \********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Layout2_vue_vue_type_template_id_600df452___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Layout2.vue?vue&type=template&id=600df452& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/assets/src/layout/Layout2.vue?vue&type=template&id=600df452&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Layout2_vue_vue_type_template_id_600df452___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Layout2_vue_vue_type_template_id_600df452___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);