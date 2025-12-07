const fbInit = callback => {
  window.fbAsyncInit = function() {
    window.FB.init({
      appId: ~window.location.origin.indexOf('dev') ? '1946861872192824' : '266741733751086',
      autoLogAppEvents: true,
      cookie: true,
      status: true,
      xfbml: true,
      version: 'v3.0'
    });

    if (typeof callback === 'function') callback();
  };

  (function(d, s, id) {
    var js,
      fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
      return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/uk_UA/sdk.js';
    fjs.parentNode.insertBefore(js, fjs);
  })(document, 'script', 'facebook-jssdk');
};
module.exports = fbInit;
