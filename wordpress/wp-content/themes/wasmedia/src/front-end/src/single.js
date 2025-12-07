import './scss/single.scss';
import 'juxtaposejs/build/js/juxtapose.min';
import Accardion from 'components/accordion';

const Covers = require('./images/covers');
const util = require('./util');

new Covers('.article-cover .wp-post-image');

const accordions = [...document.querySelectorAll('[data-component="accordion"]')];

accordions.forEach(item => {
  new Accardion(item);
});

const contentEditorList = [...document.querySelectorAll('.editor_txt ol li')];
if (contentEditorList.length > 0)
  contentEditorList.forEach(element => util.tagDecorator(element, 'span'));

window.disqus_config = function() {
  this.language = util.isLang();

  let short_link_obj = document.querySelector('link[rel=shortlink]');
  let article_obj = document.querySelector('.article-cover');

  if (short_link_obj) {
    this.page.url = short_link_obj.href;
  }

  if (article_obj) {
    this.page.title = article_obj.querySelector('.h-entry').textContent.trim();
    this.page.identifier = article_obj.dataset.postid;
  }
};

// (function() {
//   let d = document;
//   let s = d.createElement('script');
//
//   s.src = 'https://was-media.disqus.com/embed.js';
//
//   s.setAttribute('data-timestamp', +new Date());
//   (d.head || d.body).appendChild(s);
// })();
