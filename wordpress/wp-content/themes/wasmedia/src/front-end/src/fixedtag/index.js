const Item = require('./item');

function FixedTag({ elements, _opt }) {
  if (elements.length < 1) return;

  const controller = new window.ScrollMagic.Controller({
    globalSceneOptions: {
      triggerHook: 'onLeave'
    }
  });

  window.addEventListener('load', () => {
    elements.forEach(el => {
      new Item(el, controller, _opt);
    });
  });
}

module.exports = FixedTag;
