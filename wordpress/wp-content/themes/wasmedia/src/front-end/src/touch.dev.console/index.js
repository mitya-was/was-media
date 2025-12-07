const Util = require('../util/index');

function TouchConsole() {
  return this.initialize(() => {
    return this.injectConsole();
  });
}

TouchConsole.prototype.initialize = function(callback) {
  if (Util.isTouch() === false || !document.location.origin.search(/dev|frontdev/)) return this.throwException();
  if (callback && typeof callback === "function") return callback(this);
  return this;
};

TouchConsole.prototype.injectConsole = function() {
  this.container = document.createElement('div');
  this.input = document.createElement('input');
  this.output = document.createElement('output');
  this.input.type = "text";
  this.container.classList.add('touch-console');
  this.input.classList.add('touch-console-input');
  this.output.classList.add('touch-console-output');
  this.output.textContent = "...";
  this.container.appendChild(this.input);
  this.container.appendChild(this.output);
  document.body.appendChild(this.container);

  this.follow();
  return this;
};

TouchConsole.prototype.follow = function() {
  this.follower = event => {
    let value = event.target.value;
    if (value === "userAgent" || value === "ua") {
      this.print(window.navigator.userAgent);
    }
    if (value === "window" || value === "self") {
      this.print(Object.keys(window).join("\n"));
    }
    if (value === "url") {
      this.print(document.location.href);
    }
    if (value !== "window" && value !== "userAgent" && value !== "ua" && value !== "url" && value !== "self") {
      value = window[value];
      if (Util.typeOf(value) === 'object') {
        this.print(JSON.stringify(value));
      }
      if (Util.typeOf(value) === 'array') {
        this.print(value.join("\n"));
      }
      if (Util.typeOf(value) === 'number' || Util.typeOf(value) === 'string' || Util.typeOf(value) === 'boolean' || Util.typeOf(value) === 'function' || Util.typeOf(value) === 'null') {
        this.print(`${value}`);
      }
    }

    if (typeof value === "undefined" || value === "") this.clear();
  };
  ['input', 'focus', 'blur'].forEach(eventName => {
    this.input.addEventListener(eventName, this.follower);
  });
  this.input.focus();
};

TouchConsole.prototype.clear = function() {
  this.output.innerText = "...";
};

TouchConsole.prototype.print = function(msg = "") {
  if (msg === "") return;
  this.output.innerText = msg;
};

TouchConsole.prototype.throwException = function() {
  console.error('*** This device not need special console, u can use browser console ***');
  return Object.create(null);
};

module.exports = TouchConsole;
