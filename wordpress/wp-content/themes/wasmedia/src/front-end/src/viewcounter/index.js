const util = require('../util/index');

class ViewCounter {
  constructor(options) {
    this.selector = options.selector;
    this.elements = document.getElementsByClassName(this.selector);
    this.isScrolled = true;
    this.viewed = [];
  }

  updateCountView(n) {
    if (this.elements.length === this.viewed.length) return;
    Array.prototype.forEach.call(this.elements, (element) => isInViewport.apply(this, [this, element, n]));
  }
}

function isInViewport(context, element, n) {
  if (!util.isElementInVieport({ element, offset: n, isScrolled: true })) return;

  if (!context.viewed.includes(element.id)) {
    addCountView.call(context, element);
  }
}

function addCountView(elem) {
  fetch(
    `/wp-content/plugins/fox-ajax-counter/ajax_counter.php?id=${elem.id.split('-')[1]}&count=on`,
    []
  ).then(() => this.viewed.push(elem.id))
    .catch();
}

module.exports = ViewCounter;
