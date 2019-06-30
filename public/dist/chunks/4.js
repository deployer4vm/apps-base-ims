(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[4],{

/***/ "../proj-main/resources/js/views/Page1.vue":
/*!*************************************************!*\
  !*** ../proj-main/resources/js/views/Page1.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Page1_vue_vue_type_template_id_6076e399___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Page1.vue?vue&type=template&id=6076e399& */ "../proj-main/resources/js/views/Page1.vue?vue&type=template&id=6076e399&");
/* harmony import */ var _Page1_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Page1.vue?vue&type=script&lang=js& */ "../proj-main/resources/js/views/Page1.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _apps_base_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../apps-base/node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_apps_base_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Page1_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Page1_vue_vue_type_template_id_6076e399___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Page1_vue_vue_type_template_id_6076e399___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "proj-main/resources/js/views/Page1.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "../proj-main/resources/js/views/Page1.vue?vue&type=script&lang=js&":
/*!**************************************************************************!*\
  !*** ../proj-main/resources/js/views/Page1.vue?vue&type=script&lang=js& ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _apps_base_node_modules_babel_loader_lib_index_js_ref_4_0_apps_base_node_modules_vue_loader_lib_index_js_vue_loader_options_Page1_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../apps-base/node_modules/babel-loader/lib??ref--4-0!../../../../apps-base/node_modules/vue-loader/lib??vue-loader-options!./Page1.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!../proj-main/resources/js/views/Page1.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_apps_base_node_modules_babel_loader_lib_index_js_ref_4_0_apps_base_node_modules_vue_loader_lib_index_js_vue_loader_options_Page1_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "../proj-main/resources/js/views/Page1.vue?vue&type=template&id=6076e399&":
/*!********************************************************************************!*\
  !*** ../proj-main/resources/js/views/Page1.vue?vue&type=template&id=6076e399& ***!
  \********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _apps_base_node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_apps_base_node_modules_vue_loader_lib_index_js_vue_loader_options_Page1_vue_vue_type_template_id_6076e399___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../apps-base/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../apps-base/node_modules/vue-loader/lib??vue-loader-options!./Page1.vue?vue&type=template&id=6076e399& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!../proj-main/resources/js/views/Page1.vue?vue&type=template&id=6076e399&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _apps_base_node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_apps_base_node_modules_vue_loader_lib_index_js_vue_loader_options_Page1_vue_vue_type_template_id_6076e399___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _apps_base_node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_apps_base_node_modules_vue_loader_lib_index_js_vue_loader_options_Page1_vue_vue_type_template_id_6076e399___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!../proj-main/resources/js/views/Page1.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!../proj-main/resources/js/views/Page1.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
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
  metaInfo: function metaInfo() {
    return {
      title: 'Page 1'
    };
  },
  computed: {
    project_data: function project_data() {
      return this.$store.state.project.project_data;
    },
    module_data: function module_data() {
      return this.$store.state.exampleStore.example_data;
    }
  },
  methods: {
    changeData: function changeData() {
      this.$store.dispatch('exampleStore/updateData', 'Ganti data cuy');
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!../proj-main/resources/js/views/Page1.vue?vue&type=template&id=6076e399&":
/*!**************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!../proj-main/resources/js/views/Page1.vue?vue&type=template&id=6076e399& ***!
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
  return _c(
    "div",
    [
      _c("h1", [_vm._v("Page 1")]),
      _vm._v(" "),
      _c("p", [
        _vm._v(
          "prject data : " +
            _vm._s(_vm.project_data) +
            " , module data : " +
            _vm._s(_vm.module_data)
        )
      ]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "btn btn-primary",
          on: {
            click: function($event) {
              return _vm.changeData()
            }
          }
        },
        [_vm._v("change data")]
      ),
      _vm._v(" "),
      _c(
        "router-link",
        {
          staticClass: "btn btn-primary",
          attrs: { tag: "button", to: "/page2" }
        },
        [_vm._v("page 2")]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ })

}]);