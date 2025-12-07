const util = require('../util/index');

function Covers(selector) {
  this.elements = document.querySelectorAll(selector);
  this.images = [];

  this.addImages();
  this.handlers();
}

Covers.prototype.addImages = function() {
  for (let i = 0, l = this.elements.length; i < l; i += 1) {
    this.images.push(new SingleImage(this.elements[i]));
  }
};

Covers.prototype.handlers = function() {
  const self = this,
    toggleEachImageThrottled = util.throttle(toggleEachImage, 300);

  function toggleEachImage() {
    self.images.forEach(image => {
      image.toggleSrc();
    });
  }

  window.addEventListener('resize', toggleEachImageThrottled);
  window.addEventListener('DOMContentLoaded', toggleEachImage);
};

function SingleImage(item) {
  this.image = item;
  this.thumb = item.src;
  this.feature = item.dataset['feature'];

  this.image.style.display = 'block';
}

SingleImage.prototype.toggleSrc = function() {
  const src = util.isMore() ? this.feature : this.thumb;
  if (this.currentSrc === src) return;

  this.image.src = src;
  this.currentSrc = src;
  return src;
};

module.exports = Covers;
