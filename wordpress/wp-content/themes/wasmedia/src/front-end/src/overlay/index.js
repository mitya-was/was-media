function Overlay({ elements, utils }) {
  const overlays = document.querySelectorAll('.overlay');
  const body = document.body;

  elements.forEach(el => {
    const point = document.getElementById(el.getAttribute('data-target'));

    el.addEventListener('click', e => {
      let elem = e.target;

      this.goAnchor(e, utils);

      if (!elem.tagName) return false;

      if (elem.getAttribute('data-direction'))
        point.classList.add('_' + elem.getAttribute('data-direction'));

      this[elem.getAttribute('data-toggle')](point);
    });
  });

  const clear = () => {
    [].forEach.call(overlays, function(e) {
      e.classList.remove('_open');
    });
  };

  this.open = target => {
    clear();
    target.classList.add('_open');
    body.classList.add('opened-modal');
  };

  this.close = target => {
    target.classList.remove('_open');
    body.classList.remove('opened-modal');
  };

  this.toggle = target => {
    target.classList.toggle('_open');
    body.classList.toggle('opened-modal');
  };
}

Overlay.prototype.goAnchor = (e, u) => {
  let dataoptions = e.target.hasAttribute('data-options') ? e.target.dataset['options'] : '';

  if (!u.isMore(1024) && !~dataoptions.indexOf('anchor')) {
    e.preventDefault();
  }
};

module.exports = Overlay;
