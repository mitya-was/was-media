const utils = require('../util/index');

function FBFrameWindow() {
  this.opened = false;
}

FBFrameWindow.prototype.open = function() {
  const options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  const callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

  if (!options.src || this.opened === true) return;
  this.opened = true;
  const container = document.createElement('div'),
    iframe = document.createElement('iframe');

  container.classList.add('fb-window');
  iframe.setAttribute('sandbox', 'allow-scripts');
  iframe.setAttribute('allowTransparency', 'true');

  iframe.src = options.src;
  iframe.width = `${window.innerWidth}`;
  iframe.height = `${window.innerHeight}`;

  container.appendChild(iframe);
  document.body.insertAdjacentElement('afterbegin', container);

  this.container = container;
  this.container.classList.add('on');
  document.body.classList.add('fb-window-lock');
  if (utils.typeOf(callback) === 'function') callback(this);
  return this;
};

FBFrameWindow.prototype.close = function() {
  const _this = this;

  const hit = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  const callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

  if (utils.typeOf(hit) === 'function') hit();
  this.container.classList.remove('on');
  setTimeout(function() {
    _this.container.remove();
    delete _this.container;
    document.body.classList.remove('.fb-window-lock');
    _this.opened = false;
    if (utils.typeOf(callback) === 'function') callback(_this);
  }, 250);

  return this;
};

module.exports = FBFrameWindow;
