"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["AcademicSetting"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm-bundler.js");
/* harmony import */ var _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../resources/baseUrl */ "./resources/js/resources/baseUrl.js");


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data: function data() {
    return {
      academicControls: '',
      regularFirst: '',
      regularSecond: '',
      extensionFirst: '',
      extensionSecond: '',
      extensionThird: ''
    };
  },
  computed: _objectSpread(_objectSpread({}, (0,vuex__WEBPACK_IMPORTED_MODULE_2__.mapGetters)(['selectedAcademicYearId'])), {}, {
    getYearById: function getYearById() {
      return this.$store.getters.getYearById(this.selectedAcademicYearId);
    },
    checkAcademicYear: function checkAcademicYear() {
      return this.academicControls.is_closed;
    },
    checkRegularFirst: function checkRegularFirst() {
      return this.regularFirst.is_closed;
    },
    checkRegularSecond: function checkRegularSecond() {
      return this.regularSecond.is_closed;
    },
    checkExtensionFirst: function checkExtensionFirst() {
      return this.extensionFirst.is_closed;
    },
    checkExtensionSecond: function checkExtensionSecond() {
      return this.extensionSecond.is_closed;
    },
    checkExtensionThird: function checkExtensionThird() {
      return this.extensionThird.is_closed;
    },
    checkDegreeRegistrar: function checkDegreeRegistrar() {
      var _this$academicControl;

      return (_this$academicControl = this.academicControls.result_related) === null || _this$academicControl === void 0 ? void 0 : _this$academicControl.degree_registrar_result_entry_time;
    },
    checkDegreeTeacher: function checkDegreeTeacher() {
      var _this$academicControl2;

      return (_this$academicControl2 = this.academicControls.result_related) === null || _this$academicControl2 === void 0 ? void 0 : _this$academicControl2.degree_teacher_result_entry_time;
    },
    checkTvetRegistrar: function checkTvetRegistrar() {
      var _this$academicControl3;

      return (_this$academicControl3 = this.academicControls.result_related) === null || _this$academicControl3 === void 0 ? void 0 : _this$academicControl3.tvet_registrar_result_entry_time;
    },
    checkTvetTeacher: function checkTvetTeacher() {
      var _this$academicControl4;

      return (_this$academicControl4 = this.academicControls.result_related) === null || _this$academicControl4 === void 0 ? void 0 : _this$academicControl4.tvet_teacher_result_entry_time;
    }
  }),
  //change_semester_status/{id}
  methods: {
    toggleAcademicYear: function toggleAcademicYear(e) {
      var _this = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee() {
        var toggleValue, response, message;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _this.$store.commit('setIsItemLoading', true);

                _context.prev = 1;
                toggleValue = 1;
                if (e.target.checked) toggleValue = 0;
                _context.next = 6;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].put("/api/change_academic_year_status/" + _this.academicControls.id, {
                  status: toggleValue
                });

              case 6:
                response = _context.sent;

                if (!(response.status === 200)) {
                  _context.next = 13;
                  break;
                }

                _this.academicControls.is_closed = response.data;
                if (response.data) message = 'You have closed academic year';else message = 'You have opened academic year';

                _this.$store.commit('setAlertMessages', {
                  text: message,
                  type: 'success'
                });

                _context.next = 18;
                break;

              case 13:
                if (!(response.status === 202)) {
                  _context.next = 17;
                  break;
                }

                _this.$store.commit('setAlertMessages', {
                  text: 'Semesters should be closed before!',
                  type: 'danger'
                });

                _context.next = 18;
                break;

              case 17:
                throw 'Failed to fetch event';

              case 18:
                _context.next = 23;
                break;

              case 20:
                _context.prev = 20;
                _context.t0 = _context["catch"](1);

                _this.$store.commit('setAlertMessages', {
                  text: 'Faild to change academic year state',
                  type: 'danger'
                });

              case 23:
                _context.prev = 23;

                _this.$store.commit('setIsItemLoading', false);

                return _context.finish(23);

              case 26:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[1, 20, 23, 26]]);
      }))();
    },
    toggleRegularFirst: function toggleRegularFirst(e) {
      var toggleValue = 1;
      if (e.target.checked) toggleValue = 0;
      this.toggleSemester(toggleValue, this.regularFirst, 'regular semester 1');
    },
    toggleRegularSecond: function toggleRegularSecond(e) {
      var toggleValue = 1;
      if (e.target.checked) toggleValue = 0;
      this.toggleSemester(toggleValue, this.regularSecond, 'regular semester 2');
    },
    toggleExtensionFirst: function toggleExtensionFirst(e) {
      var toggleValue = 1;
      if (e.target.checked) toggleValue = 0;
      this.toggleSemester(toggleValue, this.extensionFirst, 'extension semester 1');
    },
    toggleExtensionSecond: function toggleExtensionSecond(e) {
      var toggleValue = 1;
      if (e.target.checked) toggleValue = 0;
      this.toggleSemester(toggleValue, this.extensionSecond, 'extension semester 2');
    },
    toggleExtensionThird: function toggleExtensionThird(e) {
      var toggleValue = 1;
      if (e.target.checked) toggleValue = 0;
      this.toggleSemester(toggleValue, this.extensionThird, 'extension semester 3');
    },
    toggleSemester: function toggleSemester(value, reference, semesterName) {
      var _this2 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee2() {
        var response, message;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _this2.$store.commit('setIsItemLoading', true);

                _context2.prev = 1;
                _context2.next = 4;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].put("/api/change_semester_status/" + reference.id, {
                  status: value
                });

              case 4:
                response = _context2.sent;

                if (!(response.status === 200)) {
                  _context2.next = 11;
                  break;
                }

                reference.is_closed = response.data;
                if (response.data) message = 'You have closed ' + semesterName;else message = 'You have opened ' + semesterName;

                _this2.$store.commit('setAlertMessages', {
                  text: message,
                  type: 'success'
                });

                _context2.next = 12;
                break;

              case 11:
                throw 'Failed to fetch event';

              case 12:
                _context2.next = 17;
                break;

              case 14:
                _context2.prev = 14;
                _context2.t0 = _context2["catch"](1);

                _this2.$store.commit('setAlertMessages', {
                  text: "Faild to change ".concat(semesterName, " state"),
                  type: 'danger'
                });

              case 17:
                _context2.prev = 17;

                _this2.$store.commit('setIsItemLoading', false);

                return _context2.finish(17);

              case 20:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, null, [[1, 14, 17, 20]]);
      }))();
    },
    toggleDegreeRegistrar: function toggleDegreeRegistrar(e) {
      var _this3 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee3() {
        var toggleValue, response, message;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                _this3.$store.commit('setIsItemLoading', true);

                _context3.prev = 1;
                toggleValue = 0;
                if (e.target.checked) toggleValue = 1;
                _context3.next = 6;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].put("/api/change_degree_registrar_time", {
                  entry_status: toggleValue
                });

              case 6:
                response = _context3.sent;

                if (!(response.status === 200)) {
                  _context3.next = 13;
                  break;
                }

                _this3.academicControls.result_related.change_degree_registrar_time = response.data;
                if (response.data) message = 'You have opened registrar degree result entry';else message = 'You have closed registrar degree result entry';

                _this3.$store.commit('setAlertMessages', {
                  text: message,
                  type: 'success'
                });

                _context3.next = 14;
                break;

              case 13:
                throw 'Failed to fetch event';

              case 14:
                _context3.next = 19;
                break;

              case 16:
                _context3.prev = 16;
                _context3.t0 = _context3["catch"](1);

                _this3.$store.commit('setAlertMessages', {
                  text: 'Faild to change registrar degree result entry state',
                  type: 'danger'
                });

              case 19:
                _context3.prev = 19;

                _this3.$store.commit('setIsItemLoading', false);

                return _context3.finish(19);

              case 22:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, null, [[1, 16, 19, 22]]);
      }))();
    },
    toggleTvetRegistrar: function toggleTvetRegistrar(e) {
      var _this4 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee4() {
        var toggleValue, response, message;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                _this4.$store.commit('setIsItemLoading', true);

                _context4.prev = 1;
                toggleValue = 0;
                if (e.target.checked) toggleValue = 1;
                _context4.next = 6;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].put("/api/change_tvet_registrar_time", {
                  entry_status: toggleValue
                });

              case 6:
                response = _context4.sent;

                if (!(response.status === 200)) {
                  _context4.next = 13;
                  break;
                }

                _this4.academicControls.result_related.change_tvet_registrar_time = response.data;
                if (response.data) message = 'You have opened registrar tvet result entry';else message = 'You have closed registrar tvet result entry';

                _this4.$store.commit('setAlertMessages', {
                  text: message,
                  type: 'success'
                });

                _context4.next = 14;
                break;

              case 13:
                throw 'Failed to fetch event';

              case 14:
                _context4.next = 19;
                break;

              case 16:
                _context4.prev = 16;
                _context4.t0 = _context4["catch"](1);

                _this4.$store.commit('setAlertMessages', {
                  text: 'Faild to change registrar tvet result entry state',
                  type: 'danger'
                });

              case 19:
                _context4.prev = 19;

                _this4.$store.commit('setIsItemLoading', false);

                return _context4.finish(19);

              case 22:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4, null, [[1, 16, 19, 22]]);
      }))();
    },
    toggleDegreeTeacher: function toggleDegreeTeacher(e) {
      var _this5 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee5() {
        var toggleValue, response, message;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee5$(_context5) {
          while (1) {
            switch (_context5.prev = _context5.next) {
              case 0:
                _this5.$store.commit('setIsItemLoading', true);

                _context5.prev = 1;
                toggleValue = 0;
                if (e.target.checked) toggleValue = 1;
                _context5.next = 6;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].put("/api/change_degree_teacher_time", {
                  entry_status: toggleValue
                });

              case 6:
                response = _context5.sent;

                if (!(response.status === 200)) {
                  _context5.next = 13;
                  break;
                }

                _this5.academicControls.result_related.change_degree_teacher_time = response.data;
                if (response.data) message = 'You have opened teacher degree result entry';else message = 'You have closed teacher degree result entry';

                _this5.$store.commit('setAlertMessages', {
                  text: message,
                  type: 'success'
                });

                _context5.next = 14;
                break;

              case 13:
                throw 'Failed to fetch event';

              case 14:
                _context5.next = 19;
                break;

              case 16:
                _context5.prev = 16;
                _context5.t0 = _context5["catch"](1);

                _this5.$store.commit('setAlertMessages', {
                  text: 'Faild to change teacher degree result entry state',
                  type: 'danger'
                });

              case 19:
                _context5.prev = 19;

                _this5.$store.commit('setIsItemLoading', false);

                return _context5.finish(19);

              case 22:
              case "end":
                return _context5.stop();
            }
          }
        }, _callee5, null, [[1, 16, 19, 22]]);
      }))();
    },
    toggleTvetTeacher: function toggleTvetTeacher(e) {
      var _this6 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee6() {
        var toggleValue, response, message;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee6$(_context6) {
          while (1) {
            switch (_context6.prev = _context6.next) {
              case 0:
                _this6.$store.commit('setIsItemLoading', true);

                _context6.prev = 1;
                toggleValue = 0;
                if (e.target.checked) toggleValue = 1;
                _context6.next = 6;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].put("/api/change_tvet_teacher_time", {
                  entry_status: toggleValue
                });

              case 6:
                response = _context6.sent;

                if (!(response.status === 200)) {
                  _context6.next = 13;
                  break;
                }

                _this6.academicControls.result_related.change_tvet_teacher_time = response.data;
                if (response.data) message = 'You have opened teacher tvet result entry';else message = 'You have closed teacher tvet result entry';

                _this6.$store.commit('setAlertMessages', {
                  text: message,
                  type: 'success'
                });

                _context6.next = 14;
                break;

              case 13:
                throw 'Failed to fetch event';

              case 14:
                _context6.next = 19;
                break;

              case 16:
                _context6.prev = 16;
                _context6.t0 = _context6["catch"](1);

                _this6.$store.commit('setAlertMessages', {
                  text: 'Faild to change teacher tvet result entry state',
                  type: 'danger'
                });

              case 19:
                _context6.prev = 19;

                _this6.$store.commit('setIsItemLoading', false);

                return _context6.finish(19);

              case 22:
              case "end":
                return _context6.stop();
            }
          }
        }, _callee6, null, [[1, 16, 19, 22]]);
      }))();
    },
    getData: function getData() {
      var _this7 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee7() {
        var response, _response$data$regula, _response$data$regula2, _response$data$extens, _response$data$extens2, _response$data$extens3;

        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee7$(_context7) {
          while (1) {
            switch (_context7.prev = _context7.next) {
              case 0:
                _this7.$store.commit('setIsItemLoading', true);

                _context7.prev = 1;
                _context7.next = 4;
                return _resources_baseUrl__WEBPACK_IMPORTED_MODULE_1__["default"].get("/api/get_setting_data?academic_year_id=" + _this7.selectedAcademicYearId);

              case 4:
                response = _context7.sent;

                if (!(response.status === 200)) {
                  _context7.next = 14;
                  break;
                }

                _this7.academicControls = response.data; //regular first

                _this7.regularFirst = (_response$data$regula = response.data.regular) === null || _response$data$regula === void 0 ? void 0 : _response$data$regula.find(function (semester) {
                  return semester.number === 1;
                }); //regular second

                _this7.regularSecond = (_response$data$regula2 = response.data.regular) === null || _response$data$regula2 === void 0 ? void 0 : _response$data$regula2.find(function (semester) {
                  return semester.number === 2;
                }); //extension first

                _this7.extensionFirst = (_response$data$extens = response.data.extension) === null || _response$data$extens === void 0 ? void 0 : _response$data$extens.find(function (semester) {
                  return semester.number === 1;
                }); //extension second

                _this7.extensionSecond = (_response$data$extens2 = response.data.extension) === null || _response$data$extens2 === void 0 ? void 0 : _response$data$extens2.find(function (semester) {
                  return semester.number === 2;
                }); //extension third

                _this7.extensionThird = (_response$data$extens3 = response.data.extension) === null || _response$data$extens3 === void 0 ? void 0 : _response$data$extens3.find(function (semester) {
                  return semester.number === 3;
                });
                _context7.next = 15;
                break;

              case 14:
                throw 'Failed to fetch event';

              case 15:
                _context7.prev = 15;

                _this7.$store.commit('setIsItemLoading', false);

                return _context7.finish(15);

              case 18:
              case "end":
                return _context7.stop();
            }
          }
        }, _callee7, null, [[1,, 15, 18]]);
      }))();
    }
  },
  created: function created() {
    this.getData();
  },
  watch: {
    selectedAcademicYearId: function selectedAcademicYearId() {
      this.getData();
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=template&id=302ebb5a":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=template&id=302ebb5a ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");

var _hoisted_1 = {
  "class": "border mb-3 p-2"
};
var _hoisted_2 = {
  "class": "form-switch form-check"
};
var _hoisted_3 = {
  "class": "form-check-labe fw-bold",
  "for": "year"
};
var _hoisted_4 = ["checked"];
var _hoisted_5 = {
  "class": "border mb-3 p-2"
};

var _hoisted_6 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, "Semesters", -1
/* HOISTED */
);

var _hoisted_7 = {
  "class": "d-flex"
};
var _hoisted_8 = {
  "class": "pe-5"
};

var _hoisted_9 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, "Regular", -1
/* HOISTED */
);

var _hoisted_10 = {
  "class": "form-switch form-check"
};

var _hoisted_11 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "semester1"
}, "Semester 1", -1
/* HOISTED */
);

var _hoisted_12 = ["checked"];
var _hoisted_13 = {
  "class": "form-switch form-check"
};

var _hoisted_14 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "semester2"
}, "Semester 2", -1
/* HOISTED */
);

var _hoisted_15 = ["checked"];
var _hoisted_16 = {
  "class": "ms-5"
};

var _hoisted_17 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, "Extension", -1
/* HOISTED */
);

var _hoisted_18 = {
  "class": "form-switch form-check"
};

var _hoisted_19 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "semester1e"
}, "Semester 1", -1
/* HOISTED */
);

var _hoisted_20 = ["checked"];
var _hoisted_21 = {
  "class": "form-switch form-check"
};

var _hoisted_22 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "semester2e"
}, "Semester 2", -1
/* HOISTED */
);

var _hoisted_23 = ["checked"];
var _hoisted_24 = {
  "class": "form-switch form-check"
};

var _hoisted_25 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-labe",
  "for": "semester3"
}, "Semester 3", -1
/* HOISTED */
);

var _hoisted_26 = ["checked"];
var _hoisted_27 = {
  "class": "border mb-3 p-2"
};

var _hoisted_28 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, "Student Result Entry", -1
/* HOISTED */
);

var _hoisted_29 = {
  "class": "d-flex"
};
var _hoisted_30 = {
  "class": "pe-5"
};

var _hoisted_31 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, "Registrar", -1
/* HOISTED */
);

var _hoisted_32 = {
  "class": "form-switch form-check"
};

var _hoisted_33 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "degree"
}, "Degree", -1
/* HOISTED */
);

var _hoisted_34 = ["checked"];
var _hoisted_35 = {
  "class": "form-switch form-check"
};

var _hoisted_36 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "tvet"
}, "TVET", -1
/* HOISTED */
);

var _hoisted_37 = ["checked"];
var _hoisted_38 = {
  "class": "ms-5"
};

var _hoisted_39 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, "Teacher", -1
/* HOISTED */
);

var _hoisted_40 = {
  "class": "form-switch form-check"
};

var _hoisted_41 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "degree"
}, "Degree", -1
/* HOISTED */
);

var _hoisted_42 = ["checked"];
var _hoisted_43 = {
  "class": "form-switch form-check"
};

var _hoisted_44 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
  "class": "form-check-label",
  "for": "tvet"
}, "TVET", -1
/* HOISTED */
);

var _hoisted_45 = ["checked"];
function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_base_card = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("base-card");

  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_base_card, {
    "class": "px-3 mx-4 mt-3"
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_3, "Academic Year " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.academicControls.year), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        onChange: _cache[0] || (_cache[0] = function ($event) {
          return $options.toggleAcademicYear($event);
        }),
        checked: !$options.checkAcademicYear,
        type: "checkbox",
        id: "year"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_4)])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_5, [_hoisted_6, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_8, [_hoisted_9, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [_hoisted_11, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: !$options.checkRegularFirst,
        onChange: _cache[1] || (_cache[1] = function ($event) {
          return $options.toggleRegularFirst($event);
        }),
        type: "checkbox",
        id: "semester1"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_12)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_13, [_hoisted_14, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: !$options.checkRegularSecond,
        onChange: _cache[2] || (_cache[2] = function ($event) {
          return $options.toggleRegularSecond($event);
        }),
        type: "checkbox",
        id: "semester2"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_15)])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_16, [_hoisted_17, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_18, [_hoisted_19, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: !$options.checkExtensionFirst,
        onChange: _cache[3] || (_cache[3] = function ($event) {
          return $options.toggleExtensionFirst($event);
        }),
        type: "checkbox",
        id: "semester1e"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_20)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_21, [_hoisted_22, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: !$options.checkExtensionSecond,
        onChange: _cache[4] || (_cache[4] = function ($event) {
          return $options.toggleExtensionSecond($event);
        }),
        type: "checkbox",
        id: "semester2e"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_23)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_24, [_hoisted_25, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: !$options.checkExtensionThird,
        onChange: _cache[5] || (_cache[5] = function ($event) {
          return $options.toggleExtensionThird($event);
        }),
        type: "checkbox",
        id: "semester3"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_26)])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_27, [_hoisted_28, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_29, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_30, [_hoisted_31, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_32, [_hoisted_33, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: $options.checkDegreeRegistrar,
        onChange: _cache[6] || (_cache[6] = function ($event) {
          return $options.toggleDegreeRegistrar($event);
        }),
        type: "checkbox",
        id: "degree"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_34)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_35, [_hoisted_36, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: $options.checkTvetRegistrar,
        onChange: _cache[7] || (_cache[7] = function ($event) {
          return $options.toggleTvetRegistrar($event);
        }),
        type: "checkbox",
        id: "tvet"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_37)])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_38, [_hoisted_39, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_40, [_hoisted_41, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: $options.checkDegreeTeacher,
        onChange: _cache[8] || (_cache[8] = function ($event) {
          return $options.toggleDegreeTeacher($event);
        }),
        type: "checkbox",
        id: "degreeT"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_42)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_43, [_hoisted_44, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
        "class": "form-check-input",
        checked: $options.checkTvetTeacher,
        onChange: _cache[9] || (_cache[9] = function ($event) {
          return $options.toggleTvetTeacher($event);
        }),
        type: "checkbox",
        id: "tvetT"
      }, null, 40
      /* PROPS, HYDRATE_EVENTS */
      , _hoisted_45)])])])])];
    }),
    _: 1
    /* STABLE */

  });
}

/***/ }),

/***/ "./resources/js/views/employee/dean/AcademicSetting.vue":
/*!**************************************************************!*\
  !*** ./resources/js/views/employee/dean/AcademicSetting.vue ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _AcademicSetting_vue_vue_type_template_id_302ebb5a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AcademicSetting.vue?vue&type=template&id=302ebb5a */ "./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=template&id=302ebb5a");
/* harmony import */ var _AcademicSetting_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AcademicSetting.vue?vue&type=script&lang=js */ "./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=script&lang=js");
/* harmony import */ var C_xampp_htdocs_Horizon_collage_management_system_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,C_xampp_htdocs_Horizon_collage_management_system_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_AcademicSetting_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_AcademicSetting_vue_vue_type_template_id_302ebb5a__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"resources/js/views/employee/dean/AcademicSetting.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=script&lang=js":
/*!**************************************************************************************!*\
  !*** ./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=script&lang=js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AcademicSetting_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AcademicSetting_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./AcademicSetting.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=template&id=302ebb5a":
/*!********************************************************************************************!*\
  !*** ./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=template&id=302ebb5a ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AcademicSetting_vue_vue_type_template_id_302ebb5a__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AcademicSetting_vue_vue_type_template_id_302ebb5a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./AcademicSetting.vue?vue&type=template&id=302ebb5a */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/views/employee/dean/AcademicSetting.vue?vue&type=template&id=302ebb5a");


/***/ })

}]);