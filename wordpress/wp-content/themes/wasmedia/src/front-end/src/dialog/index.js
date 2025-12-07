require('./style.scss');

class Dialog {
  constructor({ element, width, isOpen }) {
    this.element = element;
    this.width = width;
    this.isOpen = isOpen;
  }

  setInitial({ delayToShow, name }) {
    this.element.classList.remove('hide');
    this.isOpen = true;
    this.name = name;

    setTimeout(show, delayToShow, this);
  }

  close() {
    this.isOpen = false;
    hide(this);
  }
}

function show({ element, name }) {
  document.body.classList.add(`${name}-open`);
  element.classList.add('active');
}

function hide({ element, name }) {
  document.body.classList.remove(`${name}-open`);
  element.classList.remove('active');
}

module.exports = Dialog;
