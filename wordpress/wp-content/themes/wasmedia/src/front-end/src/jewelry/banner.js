const body = document.body;

class Banner {
  constructor(element, utils) {
    this.el = element;
    this.utils = utils;
    this.name = this.el.dataset['jewelryName'];
  }

  setColapseBanner() {
    this.pushPanel = this.el.querySelector('.jewelry-push');

    this.pushContent = this.el.querySelector('.jewelry-content');
    this.pushContenImage = this.pushContent.querySelector('img');

    addEventHandler.call(this);

    if (!this.pushPanel.children.length || this.pushPanel.classList.contains('b-collapse-off'))
      return false;

    if (this.el.classList.contains('jewelry-head')) document.body.classList.add('_jewelry-head');

    this.isOpen = this.pushPanel.classList.contains('b-collapse-folded') ? false : true;
    this.flag = this.isOpen;
  }
}

function addEventHandler() {
  this.pushPanel.onclick = foldToggle.bind(this);

  let updateThrottled = this.utils.throttle(() => {
    if (this.pushContent.classList.contains('jewelry-post')) {
      this.pushContent.style.height = this.pushContent.children[0].clientHeight + 'px';
    } else {
      this.pushContent.style.height = this.pushContenImage.clientHeight + 'px';
    }
  }, 800);

  window.addEventListener('load', updateThrottled);
  window.addEventListener('resize', updateThrottled);

  window.addEventListener('load', choiceCheck(foldToggle.bind(this)).bind(this));
}

function foldToggle() {
  this.pushPanel.classList.toggle('b-collapse-folded');
  this.pushPanel.classList.toggle('b-collapse-unfolded');
  body.classList.toggle('_jewelry-head__open');
  this.flag = !this.flag;
  this.utils.setCookie('jewelry-' + this.name, this.flag ? 'unfolded' : 'folded', 365);
}

function choiceCheck(f) {
  return function() {
    if (!this.utils.getCookie('jewelry-' + this.name)) return false;

    if (
      (this.isOpen !== this.utils.getCookie('jewelry-' + this.name)) === 'unfolded' ? true : false
    ) {
      return f.apply(arguments);
    }
  };
}

module.exports = Banner;
