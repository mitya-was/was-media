const utils = require('utils');

class Imgix {
  constructor(image, opts = {}) {
    this._el = image;
    this._settings = opts;

    if (!this._el) throw new Error('Must be dom element or string src');
  }

  get baseParams() {
    return extractBaseParams.call(this);
  }

  get baseUrl() {
    return buildBaseUrl.call(this);
  }

  get baseUrlWithoutQuery() {
    return this.baseUrl.split('?')[0];
  }

  get getUrl() {
    const path = utils.getLocation(this._el.src || this._el);
    return path;
  }
}

function extractBaseParams() {
  let url = this.getUrl.href;
  let queries = url.split('?')[1].split('&');
  let params = {...getDefaultParams()};

  queries.forEach(part => {
    let item = part.split('=');
    params[item[0]] = decodeURIComponent(item[1]);
  });

  params = {...params, ...this._settings};

  return params;
}

function buildBaseUrl() {
  let url = this.getUrl;
  let params = [];
  let hostname;
  let result = '';

  if (/imgix/.test(url.origin)) {
    hostname = url.origin;
  } else {
    hostname = `${url.protocol}//${url.hostname.split('.')[0]}.imgix.net`;
  }

  result = hostname + url.pathname + '?';

  for (let prop in this.baseParams) {
    let param = this.baseParams[prop];
    if (param == null) continue;

    params.push(`${encodeURIComponent(prop)}=${encodeURIComponent(param)}`);
  }

  result = result + params.join('&');

  return result;
}

function getDefaultParams() {
  const params = {
    crop: 'faces',
    fit: 'crop',
    auto: 'format',
    q: 75
  };

  return params;
}

export default Imgix;
