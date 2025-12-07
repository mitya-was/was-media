const utils = require('../util');
const url: string = `${window.location.origin}/wp-admin/admin-ajax.php`;

class Contents {
  constructor(protected element: any, private count: number = 0, private isBusy: boolean = false) {}

  getContent(printContent: any, req: string) {
    this.isBusy = true;

    postData(req)
      .then(handleResponse)
      .then(printContent)
      .catch((error: string) => console.error(error));

    this.count++;
  }

  static get targetClass() {
    return 'readMore_content';
  }
}

function postData(request: any) {
  const configInit: RequestInit = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    },
    body: utils.encodedParam(request)
  };
  return fetch(url, configInit);
}

function handleResponse(response: any) {
  return response.text().then((text: any) => {
    if (response.ok) {
      return text;
    } else {
      return new Error('No available content');
    }
  });
}

module.exports = Contents;
