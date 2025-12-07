/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/custom/2019/metro-clock/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/custom/2019/metro-clock/index.js":
/*!**********************************************!*\
  !*** ./src/custom/2019/metro-clock/index.js ***!
  \**********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ "./src/custom/2019/metro-clock/style.scss");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _template__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./template */ "./src/custom/2019/metro-clock/template.js");


/* TEMP USE ONLY */


const stickyElement = document.querySelector('#post-49227 .entry-header, #post-52578 .entry-header');
stickyElement && stickyElement.insertAdjacentHTML('afterbegin', _template__WEBPACK_IMPORTED_MODULE_1__["template"]);
/* ------------------- */

const targetTime = new Date('Oct 30, 2019 23:55:00').getTime();
const digitsList = [...document.querySelectorAll('.metro-clock .digit')];
const digitSegments = [
  [1, 2, 3, 4, 5, 6],
  [2, 3],
  [1, 2, 7, 5, 4],
  [1, 2, 7, 3, 4],
  [6, 7, 2, 3],
  [1, 6, 7, 3, 4],
  [1, 6, 5, 4, 3, 7],
  [1, 2, 3],
  [1, 2, 3, 4, 5, 6, 7],
  [1, 2, 7, 3, 6]
];

if (digitsList.length > 0) setInterval(renderClock, 1000);

function renderClock() {

  const diff = targetTime - new Date().getTime();

  if (diff >= 0) {
    const time = {
      days: Math.floor(diff / 86400000),
      hours: Math.floor((diff % 86400000) / 3600000),
      minutes: Math.floor((diff % 3600000) / 60000),
      seconds: Math.floor((diff % 60000) / 1000)
    };

    digitsList.forEach((item, i) => {
      let type = item.dataset['type'];
      const number = i % 2 === 0 ? Math.floor(time[type] / 10) : time[type] % 10;
      setNumber(item, number);
    });
  }
}

function toggleClass(digitSegment, index, segments) {
  setTimeout(() => segments[digitSegment - 1].classList.toggle('on'), index * 45);
}

function setNumber(digit, number) {
  const segments = digit.querySelectorAll('.segment');
  const current = parseInt(digit['timer'], 10);

  if (!isNaN(current) && current != number) {
    digitSegments[current].forEach((digitSegment, index) => toggleClass(digitSegment, index, segments));
  }

  if (isNaN(current) || current != number) {
    setTimeout(() => digitSegments[number].forEach((digitSegment, index) => {
      toggleClass(digitSegment, index, segments);
    }), 10);
    digit['timer'] = number;
  }
}


/***/ }),

/***/ "./src/custom/2019/metro-clock/style.scss":
/*!************************************************!*\
  !*** ./src/custom/2019/metro-clock/style.scss ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./src/custom/2019/metro-clock/template.js":
/*!*************************************************!*\
  !*** ./src/custom/2019/metro-clock/template.js ***!
  \*************************************************/
/*! exports provided: template */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "template", function() { return template; });
const segment = '<div class="segment"><span></span><span></span><span></span></div>';

const template = `<div class="metro-clock row">
                            <div class="metro-clock-shell">
                                <div class="metro-clock-shell-inner">
                                    <div class="metro-clock-shell-section">
                                        <div class="digit" data-type="days">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="days">${ segment.repeat(7) }</div>
                                    </div>
                                </div>
                            </div>
                            <div class="metro-clock-shell">
                                <div class="metro-clock-shell-inner">
                                    <div class="metro-clock-shell-section glass-flare" style="margin-right: 5px;padding-right:6px">
                                        <div class="digit" data-type="hours">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="hours">${ segment.repeat(7) }</div>
                                        <div class="spacer"></div>
                                        <div class="digit" data-type="minutes">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="minutes">${ segment.repeat(7) }</div>
                                    </div>
                                    <div class="metro-clock-shell-section glass-flare-reverse">
                                        <div class="digit" data-type="seconds">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="seconds">${ segment.repeat(7) }</div>
                                    </div>
                                </div>
                            </div>
                        </div>`;


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vc3JjL2N1c3RvbS8yMDE5L21ldHJvLWNsb2NrL2luZGV4LmpzIiwid2VicGFjazovLy8uL3NyYy9jdXN0b20vMjAxOS9tZXRyby1jbG9jay9zdHlsZS5zY3NzIiwid2VicGFjazovLy8uL3NyYy9jdXN0b20vMjAxOS9tZXRyby1jbG9jay90ZW1wbGF0ZS5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxrREFBMEMsZ0NBQWdDO0FBQzFFO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsZ0VBQXdELGtCQUFrQjtBQUMxRTtBQUNBLHlEQUFpRCxjQUFjO0FBQy9EOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxpREFBeUMsaUNBQWlDO0FBQzFFLHdIQUFnSCxtQkFBbUIsRUFBRTtBQUNySTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOzs7QUFHQTtBQUNBOzs7Ozs7Ozs7Ozs7O0FDbEZBO0FBQUE7QUFBQTtBQUFBO0FBQXNCOztBQUV0QjtBQUNzQzs7QUFFdEM7QUFDQSxnRUFBZ0Usa0RBQVE7QUFDeEU7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTs7QUFFQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBO0FBQ0E7Ozs7Ozs7Ozs7OztBQ2hFQSx5Qzs7Ozs7Ozs7Ozs7O0FDQUE7QUFBQTtBQUFBOztBQUVPO0FBQ1A7QUFDQTtBQUNBO0FBQ0EsOEVBQThFLG9CQUFvQjtBQUNsRyw4RUFBOEUsb0JBQW9CO0FBQ2xHO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxnSEFBZ0g7QUFDaEgsK0VBQStFLG9CQUFvQjtBQUNuRywrRUFBK0Usb0JBQW9CO0FBQ25HO0FBQ0EsaUZBQWlGLG9CQUFvQjtBQUNyRyxpRkFBaUYsb0JBQW9CO0FBQ3JHO0FBQ0E7QUFDQSxpRkFBaUYsb0JBQW9CO0FBQ3JHLGlGQUFpRixvQkFBb0I7QUFDckc7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiYXNzZXRzL2pzL2NhY2hlLzIwMTktbWV0cm8tY2xvY2suanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIi9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9zcmMvY3VzdG9tLzIwMTkvbWV0cm8tY2xvY2svaW5kZXguanNcIik7XG4iLCJpbXBvcnQgJy4vc3R5bGUuc2Nzcyc7XG5cbi8qIFRFTVAgVVNFIE9OTFkgKi9cbmltcG9ydCB7IHRlbXBsYXRlIH0gZnJvbSAnLi90ZW1wbGF0ZSc7XG5cbmNvbnN0IHN0aWNreUVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjcG9zdC00OTIyNyAuZW50cnktaGVhZGVyLCAjcG9zdC01MjU3OCAuZW50cnktaGVhZGVyJyk7XG5zdGlja3lFbGVtZW50ICYmIHN0aWNreUVsZW1lbnQuaW5zZXJ0QWRqYWNlbnRIVE1MKCdhZnRlcmJlZ2luJywgdGVtcGxhdGUpO1xuLyogLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xuXG5jb25zdCB0YXJnZXRUaW1lID0gbmV3IERhdGUoJ09jdCAzMCwgMjAxOSAyMzo1NTowMCcpLmdldFRpbWUoKTtcbmNvbnN0IGRpZ2l0c0xpc3QgPSBbLi4uZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLm1ldHJvLWNsb2NrIC5kaWdpdCcpXTtcbmNvbnN0IGRpZ2l0U2VnbWVudHMgPSBbXG4gIFsxLCAyLCAzLCA0LCA1LCA2XSxcbiAgWzIsIDNdLFxuICBbMSwgMiwgNywgNSwgNF0sXG4gIFsxLCAyLCA3LCAzLCA0XSxcbiAgWzYsIDcsIDIsIDNdLFxuICBbMSwgNiwgNywgMywgNF0sXG4gIFsxLCA2LCA1LCA0LCAzLCA3XSxcbiAgWzEsIDIsIDNdLFxuICBbMSwgMiwgMywgNCwgNSwgNiwgN10sXG4gIFsxLCAyLCA3LCAzLCA2XVxuXTtcblxuaWYgKGRpZ2l0c0xpc3QubGVuZ3RoID4gMCkgc2V0SW50ZXJ2YWwocmVuZGVyQ2xvY2ssIDEwMDApO1xuXG5mdW5jdGlvbiByZW5kZXJDbG9jaygpIHtcblxuICBjb25zdCBkaWZmID0gdGFyZ2V0VGltZSAtIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xuXG4gIGlmIChkaWZmID49IDApIHtcbiAgICBjb25zdCB0aW1lID0ge1xuICAgICAgZGF5czogTWF0aC5mbG9vcihkaWZmIC8gODY0MDAwMDApLFxuICAgICAgaG91cnM6IE1hdGguZmxvb3IoKGRpZmYgJSA4NjQwMDAwMCkgLyAzNjAwMDAwKSxcbiAgICAgIG1pbnV0ZXM6IE1hdGguZmxvb3IoKGRpZmYgJSAzNjAwMDAwKSAvIDYwMDAwKSxcbiAgICAgIHNlY29uZHM6IE1hdGguZmxvb3IoKGRpZmYgJSA2MDAwMCkgLyAxMDAwKVxuICAgIH07XG5cbiAgICBkaWdpdHNMaXN0LmZvckVhY2goKGl0ZW0sIGkpID0+IHtcbiAgICAgIGxldCB0eXBlID0gaXRlbS5kYXRhc2V0Wyd0eXBlJ107XG4gICAgICBjb25zdCBudW1iZXIgPSBpICUgMiA9PT0gMCA/IE1hdGguZmxvb3IodGltZVt0eXBlXSAvIDEwKSA6IHRpbWVbdHlwZV0gJSAxMDtcbiAgICAgIHNldE51bWJlcihpdGVtLCBudW1iZXIpO1xuICAgIH0pO1xuICB9XG59XG5cbmZ1bmN0aW9uIHRvZ2dsZUNsYXNzKGRpZ2l0U2VnbWVudCwgaW5kZXgsIHNlZ21lbnRzKSB7XG4gIHNldFRpbWVvdXQoKCkgPT4gc2VnbWVudHNbZGlnaXRTZWdtZW50IC0gMV0uY2xhc3NMaXN0LnRvZ2dsZSgnb24nKSwgaW5kZXggKiA0NSk7XG59XG5cbmZ1bmN0aW9uIHNldE51bWJlcihkaWdpdCwgbnVtYmVyKSB7XG4gIGNvbnN0IHNlZ21lbnRzID0gZGlnaXQucXVlcnlTZWxlY3RvckFsbCgnLnNlZ21lbnQnKTtcbiAgY29uc3QgY3VycmVudCA9IHBhcnNlSW50KGRpZ2l0Wyd0aW1lciddLCAxMCk7XG5cbiAgaWYgKCFpc05hTihjdXJyZW50KSAmJiBjdXJyZW50ICE9IG51bWJlcikge1xuICAgIGRpZ2l0U2VnbWVudHNbY3VycmVudF0uZm9yRWFjaCgoZGlnaXRTZWdtZW50LCBpbmRleCkgPT4gdG9nZ2xlQ2xhc3MoZGlnaXRTZWdtZW50LCBpbmRleCwgc2VnbWVudHMpKTtcbiAgfVxuXG4gIGlmIChpc05hTihjdXJyZW50KSB8fCBjdXJyZW50ICE9IG51bWJlcikge1xuICAgIHNldFRpbWVvdXQoKCkgPT4gZGlnaXRTZWdtZW50c1tudW1iZXJdLmZvckVhY2goKGRpZ2l0U2VnbWVudCwgaW5kZXgpID0+IHtcbiAgICAgIHRvZ2dsZUNsYXNzKGRpZ2l0U2VnbWVudCwgaW5kZXgsIHNlZ21lbnRzKTtcbiAgICB9KSwgMTApO1xuICAgIGRpZ2l0Wyd0aW1lciddID0gbnVtYmVyO1xuICB9XG59XG4iLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpbiIsImNvbnN0IHNlZ21lbnQgPSAnPGRpdiBjbGFzcz1cInNlZ21lbnRcIj48c3Bhbj48L3NwYW4+PHNwYW4+PC9zcGFuPjxzcGFuPjwvc3Bhbj48L2Rpdj4nO1xuXG5leHBvcnQgY29uc3QgdGVtcGxhdGUgPSBgPGRpdiBjbGFzcz1cIm1ldHJvLWNsb2NrIHJvd1wiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtZXRyby1jbG9jay1zaGVsbFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibWV0cm8tY2xvY2stc2hlbGwtaW5uZXJcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtZXRyby1jbG9jay1zaGVsbC1zZWN0aW9uXCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cImRpZ2l0XCIgZGF0YS10eXBlPVwiZGF5c1wiPiR7IHNlZ21lbnQucmVwZWF0KDcpIH08L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZGlnaXRcIiBkYXRhLXR5cGU9XCJkYXlzXCI+JHsgc2VnbWVudC5yZXBlYXQoNykgfTwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtZXRyby1jbG9jay1zaGVsbFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibWV0cm8tY2xvY2stc2hlbGwtaW5uZXJcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtZXRyby1jbG9jay1zaGVsbC1zZWN0aW9uIGdsYXNzLWZsYXJlXCIgc3R5bGU9XCJtYXJnaW4tcmlnaHQ6IDVweDtwYWRkaW5nLXJpZ2h0OjZweFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJkaWdpdFwiIGRhdGEtdHlwZT1cImhvdXJzXCI+JHsgc2VnbWVudC5yZXBlYXQoNykgfTwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJkaWdpdFwiIGRhdGEtdHlwZT1cImhvdXJzXCI+JHsgc2VnbWVudC5yZXBlYXQoNykgfTwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJzcGFjZXJcIj48L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZGlnaXRcIiBkYXRhLXR5cGU9XCJtaW51dGVzXCI+JHsgc2VnbWVudC5yZXBlYXQoNykgfTwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJkaWdpdFwiIGRhdGEtdHlwZT1cIm1pbnV0ZXNcIj4keyBzZWdtZW50LnJlcGVhdCg3KSB9PC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtZXRyby1jbG9jay1zaGVsbC1zZWN0aW9uIGdsYXNzLWZsYXJlLXJldmVyc2VcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZGlnaXRcIiBkYXRhLXR5cGU9XCJzZWNvbmRzXCI+JHsgc2VnbWVudC5yZXBlYXQoNykgfTwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJkaWdpdFwiIGRhdGEtdHlwZT1cInNlY29uZHNcIj4keyBzZWdtZW50LnJlcGVhdCg3KSB9PC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5gO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==