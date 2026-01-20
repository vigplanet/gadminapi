"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_views_Blogs_BlogCategories_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/BlogCategories.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/BlogCategories.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_1__);
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
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
//
//
//
//
//
//
//
//


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'BlogCategories',
  data: function data() {
    return {
      categories: [],
      create_new: false,
      edit_record: {},
      form: {
        name: '',
        slug: '',
        meta_title: '',
        meta_keywords: '',
        meta_description: '',
        status: 1
      },
      isLoading: false,
      isSubmitting: false,
      filter: '',
      filterOn: ['name', 'slug'],
      sortBy: 'id',
      sortDesc: true,
      sortDirection: 'desc',
      fields: [{
        key: 'id',
        label: __('id'),
        sortable: true,
        "class": 'text-center'
      }, {
        key: 'name',
        label: __('name'),
        sortable: true,
        "class": 'text-center'
      }, {
        key: 'slug',
        label: __('slug'),
        "class": 'text-center'
      }, {
        key: 'blogs_count',
        label: __('blogs_count'),
        "class": 'text-center'
      }, {
        key: 'status',
        label: __('status'),
        "class": 'text-center'
      }, {
        key: 'actions',
        label: __('actions'),
        "class": 'text-center'
      }],
      perPage: 10,
      currentPage: 1,
      totalRows: 0,
      pageOptions: [5, 10, 15, 20, 25, 50, 100]
    };
  },
  mounted: function mounted() {
    this.getBlogCategories();
  },
  methods: _defineProperty({
    getBlogCategories: function getBlogCategories() {
      var _this = this;
      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee() {
        var params, response;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _this.isLoading = true;
                _context.prev = 1;
                params = {
                  offset: (_this.currentPage - 1) * _this.perPage,
                  limit: _this.perPage,
                  include_inactive: 1
                };
                _context.next = 5;
                return axios__WEBPACK_IMPORTED_MODULE_1___default().get(_this.$apiUrl + '/blog_categories', {
                  params: params
                });
              case 5:
                response = _context.sent;
                if (response.data.status === 1) {
                  _this.categories = response.data.data;
                  _this.totalRows = response.data.total;
                } else {
                  _this.showMessage("error", response.data.message);
                }
                _context.next = 12;
                break;
              case 9:
                _context.prev = 9;
                _context.t0 = _context["catch"](1);
                _this.showMessage("error", __('something_went_wrong'));
              case 12:
                _context.prev = 12;
                _this.isLoading = false;
                return _context.finish(12);
              case 15:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[1, 9, 12, 15]]);
      }))();
    },
    createSlug: function createSlug() {
      if (this.form.name !== "") {
        var slug = this.form.name.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        this.form.slug = slug;
      }
    },
    saveCategory: function saveCategory() {
      var _this2 = this;
      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee2() {
        var response;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _this2.isSubmitting = true;
                _context2.prev = 1;
                if (!_this2.edit_record.id) {
                  _context2.next = 8;
                  break;
                }
                _context2.next = 5;
                return axios__WEBPACK_IMPORTED_MODULE_1___default().post(_this2.$apiUrl + "/blog_categories/update/".concat(_this2.edit_record.id), _this2.form);
              case 5:
                response = _context2.sent;
                _context2.next = 11;
                break;
              case 8:
                _context2.next = 10;
                return axios__WEBPACK_IMPORTED_MODULE_1___default().post(_this2.$apiUrl + '/blog_categories/save', _this2.form);
              case 10:
                response = _context2.sent;
              case 11:
                if (response.data.status === 1) {
                  _this2.showMessage("success", response.data.message);
                  _this2.create_new = false;
                  _this2.resetForm();
                  _this2.getBlogCategories();
                } else {
                  _this2.showMessage("error", response.data.message);
                }
                _context2.next = 17;
                break;
              case 14:
                _context2.prev = 14;
                _context2.t0 = _context2["catch"](1);
                _this2.showMessage("error", __('something_went_wrong'));
              case 17:
                _context2.prev = 17;
                _this2.isSubmitting = false;
                return _context2.finish(17);
              case 20:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, null, [[1, 14, 17, 20]]);
      }))();
    },
    deleteCategory: function deleteCategory(index, id) {
      var _this3 = this;
      this.$swal.fire({
        title: "Are you Sure?",
        text: "You want be able to revert this",
        confirmButtonText: "Yes, Sure",
        cancelButtonText: "Cancel",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#37a279',
        cancelButtonColor: '#d33'
      }).then(function (result) {
        if (result.value) {
          _this3.isLoading = true;
          axios__WEBPACK_IMPORTED_MODULE_1___default().post(_this3.$apiUrl + "/blog_categories/delete/".concat(id)).then(function (response) {
            _this3.isLoading = false;
            if (response.data.status === 1) {
              _this3.showMessage('success', response.data.message);
              _this3.getBlogCategories();
            } else {
              _this3.showMessage('error', response.data.message);
            }
          })["catch"](function (error) {
            _this3.isLoading = false;
            _this3.showMessage('error', __('something_went_wrong'));
          });
        }
      });
    },
    resetForm: function resetForm() {
      this.form = {
        name: '',
        slug: '',
        meta_title: '',
        meta_keywords: '',
        meta_description: '',
        status: 1
      };
      this.edit_record = {};
    }
  }, "createSlug", function createSlug() {
    if (this.form.name !== "") {
      var slug = this.form.name.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
      this.form.slug = slug;
    }
  }),
  watch: {
    edit_record: {
      handler: function handler(newVal) {
        if (newVal.id) {
          this.form = {
            name: newVal.name || '',
            slug: newVal.slug || '',
            meta_title: newVal.meta_title || '',
            meta_keywords: newVal.meta_keywords || '',
            meta_description: newVal.meta_description || '',
            status: newVal.status
          };
          this.create_new = true;
        }
      },
      deep: true
    },
    currentPage: function currentPage() {
      this.getBlogCategories();
    },
    perPage: function perPage() {
      this.currentPage = 1;
      this.getBlogCategories();
    }
  }
});

/***/ }),

/***/ "./resources/js/views/Blogs/BlogCategories.vue":
/*!*****************************************************!*\
  !*** ./resources/js/views/Blogs/BlogCategories.vue ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _BlogCategories_vue_vue_type_template_id_ac7e704a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BlogCategories.vue?vue&type=template&id=ac7e704a */ "./resources/js/views/Blogs/BlogCategories.vue?vue&type=template&id=ac7e704a");
/* harmony import */ var _BlogCategories_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./BlogCategories.vue?vue&type=script&lang=js */ "./resources/js/views/Blogs/BlogCategories.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _BlogCategories_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _BlogCategories_vue_vue_type_template_id_ac7e704a__WEBPACK_IMPORTED_MODULE_0__.render,
  _BlogCategories_vue_vue_type_template_id_ac7e704a__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/views/Blogs/BlogCategories.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/views/Blogs/BlogCategories.vue?vue&type=script&lang=js":
/*!*****************************************************************************!*\
  !*** ./resources/js/views/Blogs/BlogCategories.vue?vue&type=script&lang=js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BlogCategories_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./BlogCategories.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/BlogCategories.vue?vue&type=script&lang=js");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BlogCategories_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/views/Blogs/BlogCategories.vue?vue&type=template&id=ac7e704a":
/*!***********************************************************************************!*\
  !*** ./resources/js/views/Blogs/BlogCategories.vue?vue&type=template&id=ac7e704a ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_BlogCategories_vue_vue_type_template_id_ac7e704a__WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   staticRenderFns: () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_BlogCategories_vue_vue_type_template_id_ac7e704a__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_BlogCategories_vue_vue_type_template_id_ac7e704a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./BlogCategories.vue?vue&type=template&id=ac7e704a */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/BlogCategories.vue?vue&type=template&id=ac7e704a");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/BlogCategories.vue?vue&type=template&id=ac7e704a":
/*!**************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/BlogCategories.vue?vue&type=template&id=ac7e704a ***!
  \**************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render),
/* harmony export */   staticRenderFns: () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("div", { staticClass: "page-heading" }, [
        _c("div", { staticClass: "row" }, [
          _c("div", { staticClass: "col-12 col-md-6 order-md-1 order-last" }, [
            _c("h3", [_vm._v(_vm._s(_vm.__("blog_categories")))]),
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "col-12 col-md-6 order-md-2 order-first" }, [
            _c(
              "nav",
              {
                staticClass: "breadcrumb-header float-start float-lg-end",
                attrs: { "aria-label": "breadcrumb" },
              },
              [
                _c("ol", { staticClass: "breadcrumb" }, [
                  _c(
                    "li",
                    { staticClass: "breadcrumb-item" },
                    [
                      _c("router-link", { attrs: { to: "/dashboard" } }, [
                        _vm._v(_vm._s(_vm.__("dashboard"))),
                      ]),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "li",
                    {
                      staticClass: "breadcrumb-item active",
                      attrs: { "aria-current": "page" },
                    },
                    [_vm._v(_vm._s(_vm.__("blog_categories")))]
                  ),
                ]),
              ]
            ),
          ]),
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "row" }, [
          _c("div", { staticClass: "col-12 col-md-12 order-md-1 order-last" }, [
            _c("div", { staticClass: "card" }, [
              _c("div", { staticClass: "card-header" }, [
                _c("h4", [_vm._v(_vm._s(_vm.__("blog_categories")))]),
                _vm._v(" "),
                _c("span", { staticClass: "pull-right" }, [
                  _vm.$can("blog_category_create")
                    ? _c(
                        "button",
                        {
                          directives: [
                            {
                              name: "b-tooltip",
                              rawName: "v-b-tooltip.hover",
                              modifiers: { hover: true },
                            },
                          ],
                          staticClass: "btn btn-primary",
                          attrs: { title: _vm.__("add_new_category") },
                          on: {
                            click: function ($event) {
                              _vm.create_new = true
                            },
                          },
                        },
                        [_vm._v(_vm._s(_vm.__("add_category")))]
                      )
                    : _vm._e(),
                ]),
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "card-body" },
                [
                  _c(
                    "b-row",
                    { staticClass: "mb-2" },
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "3", "offset-md": "8" } },
                        [
                          _c("h6", { staticClass: "box-title" }, [
                            _vm._v(_vm._s(_vm.__("search"))),
                          ]),
                          _vm._v(" "),
                          _c("b-form-input", {
                            attrs: {
                              id: "filter-input",
                              type: "search",
                              placeholder: _vm.__("search"),
                            },
                            model: {
                              value: _vm.filter,
                              callback: function ($$v) {
                                _vm.filter = $$v
                              },
                              expression: "filter",
                            },
                          }),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { staticClass: "text-center", attrs: { md: "1" } },
                        [
                          _c(
                            "button",
                            {
                              directives: [
                                {
                                  name: "b-tooltip",
                                  rawName: "v-b-tooltip.hover",
                                  modifiers: { hover: true },
                                },
                              ],
                              staticClass: "btn btn-primary btn_refresh",
                              attrs: { title: _vm.__("refresh") },
                              on: {
                                click: function ($event) {
                                  return _vm.getBlogCategories()
                                },
                              },
                            },
                            [
                              _c("i", {
                                staticClass: "fa fa-refresh",
                                attrs: { "aria-hidden": "true" },
                              }),
                            ]
                          ),
                        ]
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("b-table", {
                    attrs: {
                      items: _vm.categories,
                      fields: _vm.fields,
                      filter: _vm.filter,
                      "filter-included-fields": _vm.filterOn,
                      "sort-by": _vm.sortBy,
                      "sort-desc": _vm.sortDesc,
                      "sort-direction": _vm.sortDirection,
                      bordered: true,
                      busy: _vm.isLoading,
                      stacked: "md",
                      "show-empty": "",
                      small: "",
                    },
                    on: {
                      "update:sortBy": function ($event) {
                        _vm.sortBy = $event
                      },
                      "update:sort-by": function ($event) {
                        _vm.sortBy = $event
                      },
                      "update:sortDesc": function ($event) {
                        _vm.sortDesc = $event
                      },
                      "update:sort-desc": function ($event) {
                        _vm.sortDesc = $event
                      },
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "table-busy",
                        fn: function () {
                          return [
                            _c(
                              "div",
                              { staticClass: "text-center text-black my-2" },
                              [
                                _c("b-spinner", {
                                  staticClass: "align-middle",
                                }),
                                _vm._v(" "),
                                _c("strong", [
                                  _vm._v(_vm._s(_vm.__("loading")) + "..."),
                                ]),
                              ],
                              1
                            ),
                          ]
                        },
                        proxy: true,
                      },
                      {
                        key: "cell(status)",
                        fn: function (row) {
                          return [
                            row.item.status == 1
                              ? _c(
                                  "span",
                                  { staticClass: "badge bg-success" },
                                  [_vm._v(_vm._s(_vm.__("active")))]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            row.item.status == 0
                              ? _c("span", { staticClass: "badge bg-danger" }, [
                                  _vm._v(_vm._s(_vm.__("deactive"))),
                                ])
                              : _vm._e(),
                          ]
                        },
                      },
                      {
                        key: "cell(blogs_count)",
                        fn: function (row) {
                          return [
                            _c("span", { staticClass: "badge bg-info" }, [
                              _vm._v(_vm._s(row.item.active_blogs_count || 0)),
                            ]),
                          ]
                        },
                      },
                      {
                        key: "cell(actions)",
                        fn: function (row) {
                          return [
                            _vm.$can("blog_category_update")
                              ? _c(
                                  "button",
                                  {
                                    directives: [
                                      {
                                        name: "b-tooltip",
                                        rawName: "v-b-tooltip.hover",
                                        modifiers: { hover: true },
                                      },
                                    ],
                                    staticClass: "btn btn-sm btn-primary",
                                    attrs: { title: _vm.__("edit") },
                                    on: {
                                      click: function ($event) {
                                        _vm.edit_record = row.item
                                      },
                                    },
                                  },
                                  [_c("i", { staticClass: "fa fa-pencil-alt" })]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.$can("blog_category_delete")
                              ? _c(
                                  "button",
                                  {
                                    directives: [
                                      {
                                        name: "b-tooltip",
                                        rawName: "v-b-tooltip.hover",
                                        modifiers: { hover: true },
                                      },
                                    ],
                                    staticClass: "btn btn-sm btn-danger",
                                    attrs: { title: _vm.__("delete") },
                                    on: {
                                      click: function ($event) {
                                        return _vm.deleteCategory(
                                          row.index,
                                          row.item.id
                                        )
                                      },
                                    },
                                  },
                                  [_c("i", { staticClass: "fa fa-trash" })]
                                )
                              : _vm._e(),
                          ]
                        },
                      },
                    ]),
                  }),
                  _vm._v(" "),
                  _c(
                    "b-row",
                    [
                      _c("b-col", { staticClass: "my-1", attrs: { md: "2" } }, [
                        _c(
                          "label",
                          [
                            _c(
                              "b-form-group",
                              {
                                staticClass: "mb-0",
                                attrs: {
                                  label: _vm.__("per_page"),
                                  "label-for": "per-page-select",
                                  "label-align-sm": "right",
                                  "label-size": "sm",
                                },
                              },
                              [
                                _c("b-form-select", {
                                  staticClass: "form-control form-select",
                                  attrs: {
                                    id: "per-page-select",
                                    options: _vm.pageOptions,
                                    size: "sm",
                                  },
                                  model: {
                                    value: _vm.perPage,
                                    callback: function ($$v) {
                                      _vm.perPage = $$v
                                    },
                                    expression: "perPage",
                                  },
                                }),
                              ],
                              1
                            ),
                          ],
                          1
                        ),
                      ]),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        {
                          staticClass: "my-1",
                          attrs: { md: "2", "offset-md": "8" },
                        },
                        [
                          _c("label", [
                            _vm._v(
                              _vm._s(_vm.__("total_records")) +
                                ":- " +
                                _vm._s(_vm.totalRows)
                            ),
                          ]),
                          _vm._v(",\n                                "),
                          _c("b-pagination", {
                            staticClass: "my-0",
                            attrs: {
                              "total-rows": _vm.totalRows,
                              "per-page": _vm.perPage,
                              align: "fill",
                              size: "sm",
                            },
                            model: {
                              value: _vm.currentPage,
                              callback: function ($$v) {
                                _vm.currentPage = $$v
                              },
                              expression: "currentPage",
                            },
                          }),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                ],
                1
              ),
            ]),
          ]),
        ]),
      ]),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title: _vm.edit_record.id
              ? _vm.__("edit_category")
              : _vm.__("add_category"),
            size: "lg",
            "hide-footer": true,
          },
          model: {
            value: _vm.create_new,
            callback: function ($$v) {
              _vm.create_new = $$v
            },
            expression: "create_new",
          },
        },
        [
          _c(
            "form",
            {
              on: {
                submit: function ($event) {
                  $event.preventDefault()
                  return _vm.saveCategory.apply(null, arguments)
                },
              },
            },
            [
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "name" } }, [
                      _vm._v(_vm._s(_vm.__("category_name")) + " "),
                      _c("span", { staticClass: "text-danger" }, [_vm._v("*")]),
                    ]),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.name,
                          expression: "form.name",
                        },
                      ],
                      staticClass: "form-control",
                      attrs: {
                        type: "text",
                        id: "name",
                        placeholder: _vm.__("enter_category_name"),
                        required: "",
                      },
                      domProps: { value: _vm.form.name },
                      on: {
                        keyup: _vm.createSlug,
                        input: function ($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.form, "name", $event.target.value)
                        },
                      },
                    }),
                  ]),
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "slug" } }, [
                      _vm._v(_vm._s(_vm.__("slug"))),
                    ]),
                    _vm._v(" "),
                    _c("span", { staticClass: "text-danger" }, [_vm._v("*")]),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.slug,
                          expression: "form.slug",
                        },
                      ],
                      staticClass: "form-control",
                      attrs: {
                        type: "text",
                        id: "slug",
                        placeholder: _vm.__("enter_slug"),
                        required: "",
                      },
                      domProps: { value: _vm.form.slug },
                      on: {
                        input: function ($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.form, "slug", $event.target.value)
                        },
                      },
                    }),
                  ]),
                ]),
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-12" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "meta_title" } }, [
                      _vm._v(_vm._s(_vm.__("meta_title"))),
                    ]),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.meta_title,
                          expression: "form.meta_title",
                        },
                      ],
                      staticClass: "form-control",
                      attrs: {
                        type: "text",
                        id: "meta_title",
                        placeholder: _vm.__("enter_meta_title"),
                      },
                      domProps: { value: _vm.form.meta_title },
                      on: {
                        input: function ($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.form, "meta_title", $event.target.value)
                        },
                      },
                    }),
                  ]),
                ]),
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "meta_keywords" } }, [
                      _vm._v(_vm._s(_vm.__("meta_keywords"))),
                    ]),
                    _vm._v(" "),
                    _c("textarea", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.meta_keywords,
                          expression: "form.meta_keywords",
                        },
                      ],
                      staticClass: "form-control",
                      attrs: {
                        id: "meta_keywords",
                        rows: "3",
                        placeholder: _vm.__("enter_meta_keywords"),
                      },
                      domProps: { value: _vm.form.meta_keywords },
                      on: {
                        input: function ($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(
                            _vm.form,
                            "meta_keywords",
                            $event.target.value
                          )
                        },
                      },
                    }),
                  ]),
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "meta_description" } }, [
                      _vm._v(_vm._s(_vm.__("meta_description"))),
                    ]),
                    _vm._v(" "),
                    _c("textarea", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.meta_description,
                          expression: "form.meta_description",
                        },
                      ],
                      staticClass: "form-control",
                      attrs: {
                        id: "meta_description",
                        rows: "3",
                        placeholder: _vm.__("enter_meta_description"),
                      },
                      domProps: { value: _vm.form.meta_description },
                      on: {
                        input: function ($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(
                            _vm.form,
                            "meta_description",
                            $event.target.value
                          )
                        },
                      },
                    }),
                  ]),
                ]),
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", [
                      _vm._v(_vm._s(_vm.__("status")) + " "),
                      _c("span", { staticClass: "text-danger" }, [_vm._v("*")]),
                    ]),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-md-9 text-left mt-1" },
                      [
                        _c("b-form-radio-group", {
                          attrs: {
                            options: [
                              { text: " Deactivated", value: 0 },
                              { text: " Activated", value: 1 },
                            ],
                            buttons: "",
                            "button-variant": "outline-primary",
                            required: "",
                          },
                          model: {
                            value: _vm.form.status,
                            callback: function ($$v) {
                              _vm.$set(_vm.form, "status", $$v)
                            },
                            expression: "form.status",
                          },
                        }),
                      ],
                      1
                    ),
                  ]),
                ]),
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "form-group text-right" }, [
                _c(
                  "button",
                  {
                    staticClass: "btn btn-secondary",
                    attrs: { type: "button" },
                    on: {
                      click: function ($event) {
                        _vm.create_new = false
                        _vm.resetForm()
                      },
                    },
                  },
                  [_vm._v(_vm._s(_vm.__("cancel")))]
                ),
                _vm._v(" "),
                _c(
                  "button",
                  {
                    staticClass: "btn btn-primary",
                    attrs: { type: "submit", disabled: _vm.isSubmitting },
                  },
                  [
                    _vm.isSubmitting
                      ? _c("span", [_vm._v(_vm._s(_vm.__("saving")) + "...")])
                      : _c("span", [_vm._v(_vm._s(_vm.__("save")))]),
                  ]
                ),
              ]),
            ]
          ),
        ]
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ })

}]);