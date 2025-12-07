const db = require('../db');
const Content = require('./content.ts');
const Dropdown = require('../dropdown');
const Slider = require('../slider');
const Link = require('../shares');
const util = require('../util');

const isHome = util.isCurrentPage('home');
const selectorClass = isHome ? '.readMore_content .was_index_load_more:last-child' : '.row .was_index_load_more:last-of-type';

module.exports = ({ elements, bottomOffset = 1000 }) => {
  if (elements.length < 1) return;

  elements.forEach(initReadMore);

  function initReadMore(element) {
    let { isOnClick, isRow } = util.getJsonString(element.dataset['options']);
    const readmoreContainer = addReadMoreContainer(element, Content.targetClass);
    const isRowClass = isRow === 'true' ? 'row' : 'col';
    const readmore = new Content(element);

    const resizeThrottled = util.throttle(function() {
      if (window.pageYOffset > util.docHeight() - bottomOffset) {
        if (readmore.isBusy) return false;

        const prefixPost = document.getElementById(window.current_post_type) || document;
        const queryIndex = prefixPost.querySelector(selectorClass);
        const query = queryIndex ? queryIndex.dataset['not'] : window.not_in;

        getContentHandler(query);
      }
    }, 1000);

    if (util.isCurrentPage('home')) {
      readmoreContainer.classList.add(isRowClass);
      element.parentElement.prepend(readmoreContainer);
    }

    if (isOnClick === 'true') {
      element.addEventListener(
        'click',
        () => {
          getContentHandler(window.not_in);
          setTimeout(() => window.addEventListener('scroll', resizeThrottled), 1000);
        },
        { once: true }
      );
    } else {
      window.addEventListener('scroll', resizeThrottled);
    }

    function getContentHandler(query) {
      if (typeof window.micro !== 'undefined' && typeof window.micro.reset !== 'undefined')
        window.micro.reset();
      let request = {
        action: 'loadmore',
        query: query,
        current_post_type: window.current_post_type,
        current_tag: window.current_tag
      };

      if (readmore.count < 1) preloader(element);

      readmore.getContent(data => printContent(data, query), request);
    }

    function printContent(data, query) {
      let updateElement = '';
      if (data.length > 0) {
        if (!isHome){
          if (readmoreContainer.closest('.tab-content')) {
            if (readmoreContainer.closest('.active')) {
              readmoreContainer.parentElement.insertAdjacentHTML('beforebegin', data);
              updateElement = readmoreContainer.parentElement.parentElement;
            }
          } else {
            readmoreContainer.parentElement.insertAdjacentHTML('beforebegin', data);
            updateElement = readmoreContainer.parentElement.parentElement;
          }
        }
        else {
          readmoreContainer.innerHTML += data;
          updateElement = readmoreContainer;
        }

        if (window.micro) setTimeout(updateModules, 1000, query, updateElement);
        readmore.isBusy = false;
      } else {
        stopPrint(element);
      }
    }

  }
};

function updateModules(postsID, HTMLelement) {
  new Dropdown({
    elements: HTMLelement.querySelectorAll('code'),
    config: db,
    utils: util
  });

  Slider({
    elements: HTMLelement.querySelectorAll('[data-component="slider"]')
  });

  new Link({
    elements: HTMLelement.querySelectorAll('[data-component="link"]')
  });

  if (typeof window.micro !== 'undefined' && typeof window.micro.update !== 'undefined') {
    window.micro.update();
  }
}

function addReadMoreContainer(element, className) {
  if (isHome) {
    const div = document.createElement('div');
    div.className = className + ' readMore-wrapper';
    return div;
  } else {
    return element;
  }
}

function stopPrint(el) {
  el.disabled = true;
  el.remove();
}

function preloader(el) {
  el.classList.add('btn-none');
  el.innerHTML = util.templateCreator`<div class="kart-loader">${'<div class="sheath"><div class="segment"></div></div>'}</div>${12}`;
}
