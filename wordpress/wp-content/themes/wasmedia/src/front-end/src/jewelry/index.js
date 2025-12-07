const Banner = require('./banner.js');
const DataSrcSet = require('../images/data-srcset.js');

class Jewelry {
  constructor({ elements, util }) {
    this._elements = elements;
    this._utils = util;
    this.getBanners();

    let d = new DataSrcSet(document.querySelectorAll('.jewelry [data-srcset]'));
    d.init();
  }

  getBanners() {
    this._elements.forEach(elem => {
      if (!elem.children.length) return;

      const banner = new Banner(elem, this._utils);
      banner.setColapseBanner();
    });
  }
}

module.exports = Jewelry;
