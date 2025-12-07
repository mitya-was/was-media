function Item(el, controller, util) {
  this.options = JSON.parse(el.dataset['options']);
  this.target = document.querySelector('.' + this.options['targetClass']);
  this.pin = document.querySelector('.' + this.options['pinClass']);
  this.util = util;
  const pushFollowers = !!this.options['pushFollowers'],
    setClassToggle = this.options['setClassToggle'] || '';

  this.scene = new window.ScrollMagic.Scene({
    triggerElement: this.target,
    duration: this.target.clientHeight
  })
    .setPin(this.pin, { pushFollowers })
    .addTo(controller);

  if (setClassToggle !== '') this.scene.setClassToggle('html.js', setClassToggle);

  if (this.options['targetEnd'] === 'true')
    this.target.style.height = this.target.clientHeight - this.pin.clientHeight * 1.35 + 'px';
  if (this.options['replace'] && this.options['replace']['position'] === 'top')
    this.scene.offset(-15);

  this.scene.on('enter progress leave', replacePosition.bind(this));
  this.scene.on('update', mobileToggle.bind(this));
}

function mobileToggle(event) {
  if (!this.util.isMore(1008) && this.options['hidemobile'] === 'true') {
    this.scene.destroy(true);
    this.target.style.height = 'auto';
  } else {
    event.currentTarget.duration(this.target.clientHeight);
  }
}

function replacePosition(event) {
  if (!this.options['replace']) return false;

  if (this.options['replace']['class'] === 'true') toggleCLass.call(this, event);
}

function toggleCLass(event) {
  if (event.type === 'enter' || (event.type === 'progress' && !this.util.isMore(1008))) {
    this.pin.classList.add('fixedTag-replace');
  } else {
    this.pin.classList.remove('fixedTag-replace');
  }
}

module.exports = Item;
