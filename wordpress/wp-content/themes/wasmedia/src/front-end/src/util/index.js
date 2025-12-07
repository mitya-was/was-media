module.exports = {
  isMore(res = 567) {
    return window.innerWidth > res;
  },
  isDevice() {
    return window.innerWidth < 1008;
  },
  isMobile() {
    return (
      /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(
        navigator.userAgent || navigator.vendor || window.opera
      ) ||
      /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw-(n|u)|c55\/|capi|ccwa|cdm-|cell|chtm|cldc|cmd-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc-s|devi|dica|dmob|do(c|p)o|ds(12|-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(-|_)|g1 u|g560|gene|gf-5|g-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd-(m|p|t)|hei-|hi(pt|ta)|hp( i|ip)|hs-c|ht(c(-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i-(20|go|ma)|i230|iac( |-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|-[a-w])|libw|lynx|m1-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|-([1-8]|c))|phil|pire|pl(ay|uc)|pn-2|po(ck|rt|se)|prox|psio|pt-g|qa-a|qc(07|12|21|32|60|-[2-7]|i-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h-|oo|p-)|sdk\/|se(c(-|0|1)|47|mc|nd|ri)|sgh-|shar|sie(-|m)|sk-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h-|v-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl-|tdg-|tel(i|m)|tim-|t-mo|to(pl|sh)|ts(70|m-|m3|m5)|tx-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas-|your|zeto|zte-/i.test(
        (navigator.userAgent || navigator.vendor || window.opera).substr(0, 4)
      )
    );
  },
  isTouch() {
    if (typeof window !== 'undefined') {
      let nav = window.navigator;
      return Boolean(
        'ontouchstart' in window ||
          nav.maxTouchPoints > 0 ||
          nav.msMaxTouchPoints > 0 ||
          (window.DocumentTouch && document instanceof window.DocumentTouch)
      );
    }
  },
  isChrome() {
    if (/CriOS/i.test(navigator.userAgent)) {
      return true;
    }
  },
  utoa(str) {
    return window.btoa(unescape(encodeURIComponent(str))).replace(/=/g, '');
  },
  externalLinks() {
    let internal = new RegExp(location.host, 'i');
    let extLinks = document.querySelectorAll('.article-content a');
    extLinks.forEach(function(e) {
      if (!internal.test(e)) {
        e.setAttribute('target', '_blank');
        if (!e.classList.contains('link-follow'))
          e.setAttribute('rel', 'nofollow noopener noreferrer');
      }
    });
  },

  getLocation(href) {
    const a = document.createElement('a');
    a.href = href;
    return a;
  },

  isLoggedIn() {
    return document.body.classList.contains('logged-in');
  },

  isCurrentPage(name) {
    return new RegExp(`(${name})+`, 'gmi').test(document.body.className);
  },

  templateCreator(string, ...values) {
    const rootStart = string[0];
    const rootEnd = string[1];
    const repeated = values[0];
    const count = values[1];
    let result = rootStart;

    for (let i = 0; i < count; i++) {
      result = result + repeated;
    }

    return (result = result + rootEnd);
  },

  getCoords({ element, tOffset = 0 }) {
    const box = element.getBoundingClientRect();

    return {
      top: box.top + tOffset + pageYOffset,
      left: box.left + pageXOffset
    };
  },

  tagDecorator(elem, tag) {
    let newTag = document.createElement(tag);
    newTag.textContent = elem.textContent;

    elem.textContent = '';
    elem.appendChild(newTag);
  },

  getJsonString(str) {
    let result;
    try {
      result = JSON.parse(str);
    } catch (e) {
      result = false;
    }
    return result;
  },

  encodedParam: function(object) {
    var encodedString = '';
    for (var prop in object) {
      if (object.hasOwnProperty(prop)) {
        if (encodedString.length > 0) {
          encodedString += '&';
        }
        encodedString += encodeURI(prop + '=' + object[prop]);
      }
    }
    return encodedString;
  },

  docHeight: function() {
    let body = document.body;
    let html = document.documentElement;

    return Math.max(
      body.scrollHeight,
      body.offsetHeight,
      html.clientHeight,
      html.scrollHeight,
      html.offsetHeight
    );
  },

  getCookie(name) {
    const result = document.cookie.match(`(^|;) ?${name}=([^;]*)(;|$)`);
    return result ? result[2] : null;
  },

  setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    const expires = `expires=${d.toUTCString()}`;
    document.cookie = `${cname}=${cvalue};${expires};path=/`;
  },

  updateState({ data, key }) {
    const currentData = localStorage.getItem(key) || '{}';
    const newData = Object.assign(JSON.parse(currentData), data);

    return localStorage.setItem(key, JSON.stringify(newData));
  },

  saveState({ data, key }) {
    const string = JSON.stringify(data);

    if (localStorage.getItem(key) === null) {
      localStorage.setItem(key, string);
    }

    return localStorage.getItem(key);
  },

  isLang() {
    return this.getCookie('pll_language');
  },

  Share: {
    telegram: function(purl, ptitle) {
      var url = 'https://t.me/share/url?';
      url += 'url=' + encodeURIComponent(purl);
      url += '&text=' + encodeURIComponent(ptitle);
      this.popup(url);
    },
    facebook: function(purl) {
      var url = 'https://www.facebook.com/sharer/sharer.php?';
      url += 'u=' + encodeURIComponent(purl);
      this.popup(url);
    },
    twitter: function(purl, ptitle) {
      var url = 'http://twitter.com/share?';
      url += 'text=' + encodeURIComponent(ptitle);
      url += '&url=' + encodeURIComponent(purl);
      url += '&counturl=' + encodeURIComponent(purl);
      this.popup(url);
    },

    popup: function(url) {
      window.open(url, '', 'toolbar=0,status=0,width=626,height=436');
    }
  },

  replaceAll: function(string, search, replacement) {
    let target = string;
    return target.replace(new RegExp(search, 'g'), replacement);
  },

  throttle: function(func, ms) {
    let isThrottled = false;
    let savedArgs;
    let savedThis;

    function wrapper() {
      if (isThrottled) {
        // (2)
        savedArgs = arguments;
        savedThis = this;
        return;
      }

      func.apply(this, arguments); // (1)

      isThrottled = true;

      setTimeout(function() {
        isThrottled = false; // (3)
        if (savedArgs) {
          wrapper.apply(savedThis, savedArgs);
          savedArgs = savedThis = null;
        }
      }, ms);
    }

    return wrapper;
  },

  save(name, { data = null }) {
    const string = JSON.stringify(data);
    localStorage.setItem(name, string);
  },
  load(name) {
    const string = localStorage.getItem(name);
    return JSON.parse(string);
  },

  createElement(tag, props, ...children) {
    const element = document.createElement(tag);

    Object.keys(props).forEach(key => {
      if (key.startsWith('data-')) {
        element.setAttribute(key, props[key]);
      } else {
        element[key] = props[key];
      }
    });

    children.forEach((child = '') => {
      let el = child;
      if (typeof el === 'string') {
        el = document.createTextNode(el);
      } else {
        el = child;
      }

      element.appendChild(el);
    });

    return element;
  },
  typeOf: function(obj) {
    return Object.prototype.toString
      .call(obj)
      .replace(/^\[object (.+)\]$/, '$1')
      .toLowerCase();
  },
  isElementInVieport: function({ element, offset, isScrolled }) {
    let pageOffset = window.pageYOffset;
    let pos = getElementOffset(element, pageOffset, offset);

    if (pos.top < pageOffset && pos.top + pos.height > pageOffset && isScrolled) {
      return true;
    }
    return false;

    function getElementOffset(el, pageoffset, offset) {
      const rect = el.getBoundingClientRect();

      return {
        top: rect.top + pageoffset - offset,
        height: rect.height
      };
    }
  }
};
