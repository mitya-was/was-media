import 'normalize.css';
import './scss/style.scss';

const Components = require('./main');

if (/^(?!.*chrome).*safari.*$/i.test(window.navigator.userAgent.toLowerCase()))
  document.body.classList.add('safari');

document.documentElement.classList.remove('no-js');
document.documentElement.classList.add('js');

//TODO: UGLY HOOK current_post_type
let t = document.querySelector('.nav-tabs-toggle .tab-link.active');

if (t) {
  t = t.getAttribute('href').slice(1);

  window.current_post_type = t;
  window.not_in = window[t + '_not_in'];
}

new Components({ element: document.body });
