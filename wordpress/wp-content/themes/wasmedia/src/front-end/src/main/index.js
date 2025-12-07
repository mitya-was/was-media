const db = require('../db');
const util = require('../util');
const Overlay = require('../overlay');
const FixedTag = require('../fixedtag');
const Link = require('../shares');
const Modals = require('../modal');
const readmore = require('../readmore');
const Slider = require('../slider');
const Dropdown = require('../dropdown');
const PushNote = require('../notification/push');
const Jewelry = require('../jewelry');
const cookieDialog = require('../features/check-cookies');
const tab = require('../tab');

class Main {
  constructor({ element }) {
    util.setCookie('pll_language', ~location.pathname.indexOf('uk') ? 'uk' : 'ru');
    if (!util.isMore(1817)) document.body.classList.add('mobile');

    new Jewelry({
      elements: getElements(element, '.jewelry'),
      util: util
    });

    new FixedTag({
      elements: getElements(element, '[data-component="fixedTag"]'),
      _opt: util
    });

    Slider({
      elements: getElements(element, '[data-component="slider"]')
    });

    new Dropdown({
      elements: getElements(element, 'code'),
      config: db,
      utils: util
    });

    new Overlay({
      elements: getElements(element, '[data-component="overlay"]'),
      utils: util
    });

    new Modals({
      elements: getElements(element, '[data-component="modal"]')
    });

    new Link({
      elements: getElements(element, '[data-component="link"]')
    });

    readmore({
      elements: getElements(element, '[data-component="readmore"]'),
      bottomOffset: 1600
    });

    cookieDialog({
      target: '.cookie-dialog'
    });

    tab({
      tabs: getElements(document, '.nav-tabs-toggle .tab-link'),
      panes: getElements(document, '.tab-pane')
    });

    if (window.OneSignal) window.OneSignal.push(PushNote);
  }
}

function getElements(parent = document.body, selector = '') {
  return [...parent.querySelectorAll(selector)];
}

module.exports = Main;
