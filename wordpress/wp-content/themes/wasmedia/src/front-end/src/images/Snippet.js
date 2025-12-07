const utils = require('utils');
import Imgix from '@/images/Imgix';

class Snippet extends Imgix {
  constructor(el, settings) {
    super(el, {...settings});
  }

  get snippetUrlTxt() {
    return this.getBaseUrl() + this._txt64;
  }

  set snippetUrlTxt(txt) {
    this._txt64 =
      '&txt64=' +
      utils.utoa(txt).replace(/=/g, '') +
      '&txtalign=center,middle&txtfit=max&txtclr=fff&txtpad=150&txtsize=40&txtfont64=SGVsdmV0aWNhIE5ldWUgQ29uZGVuc2VkLEJvbGQ';
  }

  getBaseUrl() {
    return super.baseUrl;
  }

  static createFacebookSnippet(el) {
    return new Snippet(el, {w: 1200, h: 630, q: 80, exp: -5});
  }
}

export default Snippet;
