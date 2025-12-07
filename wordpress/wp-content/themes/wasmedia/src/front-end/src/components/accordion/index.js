import 'components/accordion/style.scss';

class Accardion {
  constructor(element) {
    this.element = element;

    init.call(this);
  }

  togglePanel(event) {
    const target = event.target;

    if (!target.classList.contains('accordion-header')) return;

    target.classList.toggle('active');
    const panel = target.nextElementSibling;

    panel.classList.toggle('shown');
    panel.style.maxHeight = panel.style.maxHeight ? null : panel.scrollHeight + 'px';
  }
}

function init() {
  this.element.addEventListener('click', this.togglePanel);
}

export default Accardion;
