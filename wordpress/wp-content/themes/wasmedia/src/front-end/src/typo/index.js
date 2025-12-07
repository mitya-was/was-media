function Typo() {
}

Typo.prototype.typeOf = function(object = null) {
  return Object.prototype.toString
    .call(object)
    .replace(/^\[object (.+)\]$/, '$1')
    .toLowerCase();
};

Typo.prototype.typify = function(object = null) {
  return Object.freeze({
    type: this.typeOf(object),
    object: object
  });
};

Typo.prototype.isDef = function(object = null) {
  return (
    !/null|undefined/.test(this.typeOf(object)) ||
    (this.typeOf(object) === 'number' && !isNaN(object))
  );
};

Typo.prototype.isFn = function(object = null) {
  return /function/.test(this.typeOf(object));
};

Typo.prototype.isNumber = function(object = null) {
  return this.typeOf(object) === 'number' && !isNaN(object) && isFinite(object);
};

Typo.prototype.isFloat = function(object = null) {
  return this.isNumber(object) && /[.]/.test(`${object}`);
};

Typo.prototype.isInteger = function(object = null) {
  return this.isNumber(object) && !/[.]/.test(`${object}`);
};

Typo.prototype.isHEX = function(object = null) {
  object = object.replace(/[#]|[0x]/g, '');
  return parseInt(object, 16).toString(16) === object;
};

Typo.prototype.isElement = function(object = null) {
  return /^(html)+(.)+(element)$|htmlelement/gm.test(this.typeOf(object));
};

Typo.prototype.isEmpty = function(object = null) {
  let type = this.typeOf(object),
    response = false;
  if (!this.isDef(object)) return true;
  if (type === 'string' && object === '') {
    response = true;
  }
  if (/array|htmlcollection|nodelist/.test(type) && object.length === 0) {
    response = true;
  }
  if (/set|map/.test(type) && !object.size) {
    response = true;
  }
  if (type === 'object' && !Object.keys(object).length) {
    response = true;
  }
  if (this.isElement(object) && (!object.children.length && !object.childNodes.length)) {
    response = true;
  }
  return response;
};

Typo.prototype.isChar = function(object = null) {
  return this.typeOf(object) === 'string' && object.length === 1;
};

Typo.prototype.isURL = function(object = null) {
  return this.typeOf(object) === 'string' && /(https?:\/\/[^\s]+)/g.test(object);
};

Typo.prototype.isTouch = function(ctx = null) {
  if (this.isDef(window)) ctx = window;
  if (!this.isDef(ctx)) return false;
  return 'ontouchstart' in ctx || navigator.MaxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
};

module.exports = new Typo();
