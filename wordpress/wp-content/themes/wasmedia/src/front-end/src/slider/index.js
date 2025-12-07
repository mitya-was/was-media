require('./style.scss');
const util = require('../util');
const CONF = require('./config');
const wSwiper = window.Swiper;

const header = document.getElementById('header');

const Slider = ({ elements }) => {
  elements.forEach(item => {
    const options = item.dataset['config'];
    const slider = new wSwiper(item, CONF[options]);

    if (options === 'default') {
      slider.on('click', function(e) {
        if (e.target.closest('.swiper-wrapper')) {
          this.slideNext();
        }
      });
    }

    if (util.isMobile() && options === 'popular') {
      let interval = setInterval(() => {
        if (!header.classList.contains('_open')) return;
        slider.init();
        clearInterval(interval);
      }, 2000);
    }
  });
};

module.exports = Slider;
