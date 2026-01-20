"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_views_Blogs_Blogs_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/Blogs.vue?vue&type=script&lang=js":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/Blogs.vue?vue&type=script&lang=js ***!
  \************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _tinymce_tinymce_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @tinymce/tinymce-vue */ "./node_modules/@tinymce/tinymce-vue/lib/es2015/main/ts/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_2__);

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
  name: 'Blogs',
  components: {
    Editor: _tinymce_tinymce_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  data: function data() {
    return {
      blogs: [],
      categories: [],
      create_new: false,
      edit_record: {},
      form: {
        title: '',
        slug: '',
        category_id: '',
        image: null,
        image_url: '',
        description: '',
        meta_title: '',
        meta_keywords: '',
        meta_description: '',
        status: 1
      },
      isLoading: false,
      isSubmitting: false,
      filter: '',
      filterOn: ['title', 'description'],
      sortBy: 'id',
      sortDesc: true,
      sortDirection: 'desc',
      selectedCategory: '',
      fields: [{
        key: 'id',
        label: __('id'),
        sortable: true,
        "class": 'text-center'
      }, {
        key: 'image',
        label: __('image'),
        "class": 'text-center'
      }, {
        key: 'title',
        label: __('title'),
        sortable: true,
        "class": 'text-center'
      }, {
        key: 'category',
        label: __('category'),
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
      pageOptions: [5, 10, 15, 20, 25, 50, 100],
      descriptionValidation: '' // For native HTML5 validation synced with TinyMCE
    };
  },
  mounted: function mounted() {
    this.getBlogs();
    this.getCategories();
  },
  methods: {
    // Get all blogs
    getBlogs: function getBlogs() {
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
                  limit: _this.perPage,
                  offset: (_this.currentPage - 1) * _this.perPage,
                  include_inactive: 1
                };
                if (_this.selectedCategory) {
                  params.category_id = _this.selectedCategory;
                }
                _context.next = 6;
                return axios__WEBPACK_IMPORTED_MODULE_2___default().get(_this.$apiUrl + '/blogs/', {
                  params: params
                });
              case 6:
                response = _context.sent;
                if (response.data.status === 1) {
                  _this.blogs = response.data.data;
                  _this.totalRows = response.data.total;
                } else {
                  _this.showMessage("error", response.data.message);
                }
                _context.next = 13;
                break;
              case 10:
                _context.prev = 10;
                _context.t0 = _context["catch"](1);
                _this.showMessage("error", __('something_went_wrong'));
              case 13:
                _context.prev = 13;
                _this.isLoading = false;
                return _context.finish(13);
              case 16:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[1, 10, 13, 16]]);
      }))();
    },
    getCategories: function getCategories() {
      var _this2 = this;
      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee2() {
        var response;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _context2.prev = 0;
                _context2.next = 3;
                return axios__WEBPACK_IMPORTED_MODULE_2___default().get(_this2.$apiUrl + '/blog_categories/dropdown');
              case 3:
                response = _context2.sent;
                if (response.data.status === 1) {
                  _this2.categories = response.data.data;
                }
                _context2.next = 10;
                break;
              case 7:
                _context2.prev = 7;
                _context2.t0 = _context2["catch"](0);
                console.error('Error fetching categories:', _context2.t0);
              case 10:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, null, [[0, 7]]);
      }))();
    },
    handleImageUpload: function handleImageUpload(event) {
      var file = event.target.files[0];
      if (file) {
        this.form.image = file;
        this.form.image_url = URL.createObjectURL(file);
      }
    },
    saveBlog: function saveBlog(event) {
      var _this3 = this;
      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee3() {
        var form, firstInvalid, formData, response;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                // Update description validation before submitting
                _this3.updateDescriptionValidation();
                form = event.target;
                if (form.checkValidity()) {
                  _context3.next = 6;
                  break;
                }
                firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                  firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                  });
                  setTimeout(function () {
                    firstInvalid.focus();
                    firstInvalid.reportValidity();
                  }, 100);
                } else {
                  form.reportValidity();
                }
                return _context3.abrupt("return");
              case 6:
                _this3.isSubmitting = true;
                _context3.prev = 7;
                formData = new FormData();
                formData.append('title', _this3.form.title);
                formData.append('slug', _this3.form.slug);
                formData.append('category_id', _this3.form.category_id);
                formData.append('description', _this3.form.description);
                formData.append('meta_title', _this3.form.meta_title);
                formData.append('meta_keywords', _this3.form.meta_keywords);
                formData.append('meta_description', _this3.form.meta_description);
                formData.append('status', _this3.form.status);
                if (_this3.form.image) {
                  formData.append('image', _this3.form.image);
                }
                if (!_this3.edit_record.id) {
                  _context3.next = 24;
                  break;
                }
                _context3.next = 21;
                return axios__WEBPACK_IMPORTED_MODULE_2___default().post(_this3.$apiUrl + "/blogs/update/".concat(_this3.edit_record.id), formData, {
                  headers: {
                    'Content-Type': 'multipart/form-data'
                  }
                });
              case 21:
                response = _context3.sent;
                _context3.next = 27;
                break;
              case 24:
                _context3.next = 26;
                return axios__WEBPACK_IMPORTED_MODULE_2___default().post(_this3.$apiUrl + '/blogs/save', formData, {
                  headers: {
                    'Content-Type': 'multipart/form-data'
                  }
                });
              case 26:
                response = _context3.sent;
              case 27:
                if (response.data.status === 1) {
                  _this3.showMessage("success", response.data.message);
                  _this3.create_new = false;
                  _this3.resetForm();
                  _this3.getBlogs();
                } else {
                  _this3.showMessage("error", response.data.message);
                }
                _context3.next = 33;
                break;
              case 30:
                _context3.prev = 30;
                _context3.t0 = _context3["catch"](7);
                _this3.showMessage("error", __('something_went_wrong'));
              case 33:
                _context3.prev = 33;
                _this3.isSubmitting = false;
                return _context3.finish(33);
              case 36:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, null, [[7, 30, 33, 36]]);
      }))();
    },
    deleteBlog: function deleteBlog(index, id) {
      var _this4 = this;
      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee4() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                _this4.$swal.fire({
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
                    _this4.isLoading = true;
                    axios__WEBPACK_IMPORTED_MODULE_2___default().post(_this4.$apiUrl + "/blogs/delete/".concat(id)).then(function (response) {
                      _this4.isLoading = false;
                      if (response.data.status === 1) {
                        _this4.showMessage('success', response.data.message);
                        _this4.getBlogs();
                      } else {
                        _this4.showMessage('error', response.data.message);
                      }
                    })["catch"](function (error) {
                      _this4.isLoading = false;
                      _this4.showMessage('error', __('something_went_wrong'));
                    });
                  }
                });
              case 1:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4);
      }))();
    },
    resetForm: function resetForm() {
      this.form = {
        title: '',
        slug: '',
        category_id: '',
        image: null,
        image_url: '',
        description: '',
        meta_title: '',
        meta_keywords: '',
        meta_description: '',
        status: 1
      };
      this.edit_record = {};
      this.descriptionValidation = ''; // Reset validation field
    },
    // Create slug from title
    createSlug: function createSlug() {
      if (this.form.title !== "") {
        var slug = this.form.title.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        this.form.slug = slug;
      }
    },
    // Open add modal - reset form first
    openAddModal: function openAddModal() {
      this.resetForm();
      this.create_new = true;
    },
    // Update hidden validation input based on TinyMCE content
    updateDescriptionValidation: function updateDescriptionValidation() {
      // Extract text content from HTML, removing all tags
      var textContent = this.form.description ? this.form.description.replace(/<[^>]*>/g, '').trim() : '';
      this.descriptionValidation = textContent;
    }
  },
  watch: {
    // Watch for edit_record changes to populate form
    edit_record: {
      handler: function handler(newVal) {
        var _this5 = this;
        if (newVal.id) {
          this.form = {
            title: newVal.title || '',
            slug: newVal.slug || '',
            category_id: newVal.category_id || '',
            image: null,
            image_url: newVal.image_url || '',
            description: newVal.description || '',
            meta_title: newVal.meta_title || '',
            meta_keywords: newVal.meta_keywords || '',
            meta_description: newVal.meta_description || '',
            status: newVal.status
          };
          // Update validation field when editing
          this.$nextTick(function () {
            _this5.updateDescriptionValidation();
          });
          this.create_new = true;
        }
      },
      deep: true
    },
    // Watch description changes to update validation
    'form.description': function formDescription() {
      this.updateDescriptionValidation();
    },
    // Watch for pagination changes
    currentPage: function currentPage() {
      this.getBlogs();
    },
    // Watch for per page changes
    perPage: function perPage() {
      this.currentPage = 1; // Reset to first page when changing per page
      this.getBlogs();
    }
  }
});

/***/ }),

/***/ "./resources/js/views/Blogs/Blogs.vue":
/*!********************************************!*\
  !*** ./resources/js/views/Blogs/Blogs.vue ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Blogs_vue_vue_type_template_id_3efd3ca4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Blogs.vue?vue&type=template&id=3efd3ca4 */ "./resources/js/views/Blogs/Blogs.vue?vue&type=template&id=3efd3ca4");
/* harmony import */ var _Blogs_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Blogs.vue?vue&type=script&lang=js */ "./resources/js/views/Blogs/Blogs.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Blogs_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _Blogs_vue_vue_type_template_id_3efd3ca4__WEBPACK_IMPORTED_MODULE_0__.render,
  _Blogs_vue_vue_type_template_id_3efd3ca4__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/views/Blogs/Blogs.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/views/Blogs/Blogs.vue?vue&type=script&lang=js":
/*!********************************************************************!*\
  !*** ./resources/js/views/Blogs/Blogs.vue?vue&type=script&lang=js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Blogs_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Blogs.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/Blogs.vue?vue&type=script&lang=js");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Blogs_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/views/Blogs/Blogs.vue?vue&type=template&id=3efd3ca4":
/*!**************************************************************************!*\
  !*** ./resources/js/views/Blogs/Blogs.vue?vue&type=template&id=3efd3ca4 ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Blogs_vue_vue_type_template_id_3efd3ca4__WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   staticRenderFns: () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Blogs_vue_vue_type_template_id_3efd3ca4__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Blogs_vue_vue_type_template_id_3efd3ca4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Blogs.vue?vue&type=template&id=3efd3ca4 */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/Blogs.vue?vue&type=template&id=3efd3ca4");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/Blogs.vue?vue&type=template&id=3efd3ca4":
/*!*****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/Blogs/Blogs.vue?vue&type=template&id=3efd3ca4 ***!
  \*****************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render),
/* harmony export */   staticRenderFns: () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var this$1 = this
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("div", { staticClass: "page-heading" }, [
        _c("div", { staticClass: "row" }, [
          _c("div", { staticClass: "col-12 col-md-6 order-md-1 order-last" }, [
            _c("h3", [_vm._v(_vm._s(_vm.__("blogs")))]),
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
                    [_vm._v(_vm._s(_vm.__("blogs")))]
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
                _c("h4", [_vm._v(_vm._s(_vm.__("blogs")))]),
                _vm._v(" "),
                _c("span", { staticClass: "pull-right" }, [
                  _vm.$can("blog_create")
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
                          attrs: { title: _vm.__("add_new_blog") },
                          on: { click: _vm.openAddModal },
                        },
                        [_vm._v(_vm._s(_vm.__("add_blog")))]
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
                        { attrs: { md: "2" } },
                        [
                          _c("h6", { staticClass: "box-title" }, [
                            _vm._v(_vm._s(_vm.__("category"))),
                          ]),
                          _vm._v(" "),
                          _c(
                            "b-form-select",
                            {
                              staticClass: "form-control form-select",
                              on: {
                                change: function ($event) {
                                  return _vm.getBlogs()
                                },
                              },
                              model: {
                                value: _vm.selectedCategory,
                                callback: function ($$v) {
                                  _vm.selectedCategory = $$v
                                },
                                expression: "selectedCategory",
                              },
                            },
                            [
                              _c("option", { attrs: { value: "" } }, [
                                _vm._v(_vm._s(_vm.__("all_categories"))),
                              ]),
                              _vm._v(" "),
                              _vm._l(_vm.categories, function (category) {
                                return _c(
                                  "option",
                                  {
                                    key: category.id,
                                    domProps: { value: category.id },
                                  },
                                  [_vm._v(_vm._s(category.name))]
                                )
                              }),
                            ],
                            2
                          ),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { attrs: { md: "3", "offset-md": "5" } },
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
                                  return _vm.getBlogs()
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
                      items: _vm.blogs,
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
                        key: "cell(image)",
                        fn: function (row) {
                          return [
                            row.item.image_url
                              ? _c("img", {
                                  attrs: {
                                    src: row.item.image_url,
                                    height: "50",
                                  },
                                })
                              : _c("span", { staticClass: "text-muted" }, [
                                  _vm._v(_vm._s(_vm.__("no_image"))),
                                ]),
                          ]
                        },
                      },
                      {
                        key: "cell(category)",
                        fn: function (row) {
                          return [
                            _c("span", [
                              _vm._v(
                                _vm._s(
                                  row.item.category
                                    ? row.item.category.name
                                    : "-"
                                )
                              ),
                            ]),
                          ]
                        },
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
                        key: "cell(actions)",
                        fn: function (row) {
                          return [
                            _vm.$can("blog_update")
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
                            _vm.$can("blog_delete")
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
                                        return _vm.deleteBlog(
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
              ? _vm.__("edit_blog")
              : _vm.__("add_blog"),
            size: "xl",
            "hide-footer": true,
          },
          on: { hide: _vm.resetForm },
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
              attrs: { enctype: "multipart/form-data" },
              on: {
                submit: function ($event) {
                  $event.preventDefault()
                  return _vm.saveBlog.apply(null, arguments)
                },
              },
            },
            [
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "title" } }, [
                      _vm._v(_vm._s(_vm.__("title")) + " "),
                      _c("span", { staticClass: "text-danger" }, [_vm._v("*")]),
                    ]),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.title,
                          expression: "form.title",
                        },
                      ],
                      staticClass: "form-control",
                      attrs: {
                        type: "text",
                        id: "title",
                        placeholder: _vm.__("enter_blog_title"),
                        required: "",
                      },
                      domProps: { value: _vm.form.title },
                      on: {
                        keyup: _vm.createSlug,
                        input: function ($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.form, "title", $event.target.value)
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
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "category_id" } }, [
                      _vm._v(_vm._s(_vm.__("category")) + " "),
                      _c("span", { staticClass: "text-danger" }, [_vm._v("*")]),
                    ]),
                    _vm._v(" "),
                    _c(
                      "select",
                      {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.form.category_id,
                            expression: "form.category_id",
                          },
                        ],
                        staticClass: "form-control form-select",
                        attrs: {
                          id: "category_id",
                          name: "category_id",
                          required: "",
                        },
                        on: {
                          change: function ($event) {
                            var $$selectedVal = Array.prototype.filter
                              .call($event.target.options, function (o) {
                                return o.selected
                              })
                              .map(function (o) {
                                var val = "_value" in o ? o._value : o.value
                                return val
                              })
                            _vm.$set(
                              _vm.form,
                              "category_id",
                              $event.target.multiple
                                ? $$selectedVal
                                : $$selectedVal[0]
                            )
                          },
                        },
                      },
                      [
                        _c("option", { attrs: { value: "" } }, [
                          _vm._v(_vm._s(_vm.__("select_category"))),
                        ]),
                        _vm._v(" "),
                        _vm._l(_vm.categories, function (category) {
                          return _c(
                            "option",
                            {
                              key: category.id,
                              domProps: { value: category.id },
                            },
                            [_vm._v(_vm._s(category.name))]
                          )
                        }),
                      ],
                      2
                    ),
                  ]),
                ]),
                _vm._v(" "),
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
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-6" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("label", { attrs: { for: "image" } }, [
                      _vm._v(_vm._s(_vm.__("image"))),
                    ]),
                    _vm._v(" "),
                    !_vm.edit_record.id || !_vm.form.image_url
                      ? _c("span", { staticClass: "text-danger" }, [
                          _vm._v("*"),
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    _c("input", {
                      staticClass: "form-control",
                      attrs: {
                        type: "file",
                        id: "image",
                        accept: "image/*",
                        required: !_vm.edit_record.id || !_vm.form.image_url,
                      },
                      on: { change: _vm.handleImageUpload },
                    }),
                    _vm._v(" "),
                    _c("small", { staticClass: "text-muted" }, [
                      _vm._v(
                        _vm._s(_vm.__("supported_formats")) +
                          ": JPG, PNG, GIF (" +
                          _vm._s(_vm.__("max_size")) +
                          ": 2MB)"
                      ),
                    ]),
                    _vm._v(" "),
                    _vm.form.image_url
                      ? _c("div", { staticClass: "mt-2" }, [
                          _c("img", {
                            staticClass: "img-thumbnail",
                            attrs: { src: _vm.form.image_url, height: "100" },
                          }),
                          _vm._v(" "),
                          _vm.edit_record.id
                            ? _c("p", { staticClass: "text-muted mt-1" }, [
                                _vm._v(
                                  _vm._s(_vm.__("current_image")) +
                                    " - " +
                                    _vm._s(
                                      _vm.__("leave_empty_to_keep_current")
                                    )
                                ),
                              ])
                            : _vm._e(),
                        ])
                      : _vm._e(),
                  ]),
                ]),
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-md-12" }, [
                  _c(
                    "div",
                    { staticClass: "form-group" },
                    [
                      _c("label", { attrs: { for: "description" } }, [
                        _vm._v(_vm._s(_vm.__("description")) + " "),
                        _c("span", { staticClass: "text-danger" }, [
                          _vm._v("*"),
                        ]),
                      ]),
                      _vm._v(" "),
                      _c("editor", {
                        attrs: {
                          init: {
                            height: 300,
                            plugins: this.$editorPlugins,
                            toolbar: this.$editorToolbar,
                            font_size_formats: this.$editorFont_size_formats,
                            content_style:
                              "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }",
                            setup: function (editor) {
                              editor.on("change", function () {
                                this$1.updateDescriptionValidation()
                              })
                            },
                          },
                          placeholder: _vm.__("enter_blog_description"),
                        },
                        on: { input: _vm.updateDescriptionValidation },
                        model: {
                          value: _vm.form.description,
                          callback: function ($$v) {
                            _vm.$set(_vm.form, "description", $$v)
                          },
                          expression: "form.description",
                        },
                      }),
                      _vm._v(" "),
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.descriptionValidation,
                            expression: "descriptionValidation",
                          },
                        ],
                        staticClass: "form-control",
                        staticStyle: {
                          height: "2px",
                          padding: "0",
                          "margin-top": "2px",
                          "font-size": "1px",
                          "line-height": "2px",
                        },
                        attrs: {
                          type: "text",
                          id: "description_validation",
                          required: "",
                          tabindex: "-1",
                          "aria-label": "Description validation",
                        },
                        domProps: { value: _vm.descriptionValidation },
                        on: {
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.descriptionValidation = $event.target.value
                          },
                        },
                      }),
                    ],
                    1
                  ),
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
