const util = require('../util/index');

function History({ selector }) {
  this._current;
  this._selector = selector;

  this.historyArticles = getArticles(this._selector);

  if (!Array.isArray(this.historyArticles) || !this.historyArticles.length) return;

  this.scrollThrottled = util.throttle(() => {
    this.historyArticles.forEach(elem => {
      if (util.isElementInVieport({ element: elem, offset: 150, isScrolled: true })) {
        if (!this._current || !this._current.includes(elem.dataset['componentSlug'])) {
          let path = window.location.pathname.split('/')[2];
          this._current = `/microformats/${elem.dataset['componentSlug']}/`;
          window.history.replaceState({ micro: path }, null, this._current);
        }
      }
    });
  }, 800);

  window.addEventListener('popstate', event => {
    if (!event.state) return false;
    document.querySelector('.' + event.state.micro).scrollIntoView({
      behavior: 'smooth'
      //block: 'start' // scroll to top of target element
    });
  });

  document.addEventListener('scroll', this.scrollThrottled.bind(this));
}

History.prototype.reset = function() {
  document.removeEventListener('scroll', this.scrollThrottled);
};

History.prototype.update = function() {
  this.reset();
  document.addEventListener('scroll', this.scrollThrottled);
  this.historyArticles = getArticles(this._selector);
};

function getArticles(selector) {
  return [...document.getElementsByClassName(selector)];
}

module.exports = History;
