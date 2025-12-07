require('tingle.js/src/tingle.css');
require('./style.scss');

function Modals({ elements }) {
  this._el = elements;

  elements.forEach(elem => {
    const theme = 'theme' in elem.dataset ? elem.dataset['theme'] : 'modal-default';

    const content = document.querySelector(elem.dataset['target']);

    let modal = new window.tingle.modal({
      footer: false,
      stickyFooter: false,
      closeMethods: ['overlay', 'button', 'escape'],
      closeLabel: '',
      cssClass: [theme],
      onOpen() {
        if (content.querySelector('input[type="search"]')) {
          content.querySelector('input[type="search"]').focus();
        }
      }
    });

    // set content
    modal.setContent(content);

    elem.onclick = () => modal.open();
  });
}

module.exports = Modals;
