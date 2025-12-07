require('./style.scss');
const utils = require('../../util');
const Dialog = require('../../dialog');
const storageKey = 'cookie-dialog';

const cookiesDialog = ({ target, isOpen = true, width = '100%' }) => {
  const element = document.querySelector(target);
  const dialog = new Dialog({ element, isOpen, width });

  if (!element) {
    return false;
  }

  if (!localStorage.getItem(storageKey)) {
    dialog.setInitial({ delayToShow: 2000, name: storageKey });
  }

  element.addEventListener('click', dialogHandler.bind(null, dialog));
};

function dialogHandler(dialog, event) {
  let target = event.target;
  if (target.classList.contains('cookie-dialog-close')) {
    utils.saveState({
      data: {
        isOpen: false
      },
      key: storageKey
    });
    dialog.close();
  }
}

module.exports = cookiesDialog;
