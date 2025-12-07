const utils = require('../util');

function StatusChangeCallback(context, numberCorrect) {
  let sharesDiv = document.querySelector('.article_meta_media .btn-fb').cloneNode(true);
  sharesDiv.classList.remove('btn-block');
  sharesDiv.classList.remove('btn-brand');
  let status = document.querySelector('.gameOutro__lottery .inner_content');
  let isOn = false;

  let template = `
        <p class="result_hdr">Чтобы принять участие:</p>
        <p class="resultList">1. Войдите под своим аккаунтом в Facebook.</p>
        <div onlogin="checkLoginState();" class="fb-login-button" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-scope="public_profile" data-auto-logout-link="false" data-use-continue-as="false"></div>
        <p class="resultList">2. Опубликуйте результат теста на своей странице.</p>
        `;

  if (document.querySelector('html.js').getAttribute('lang') == 'uk-UK'){
    template = `
        <p class="result_hdr">Щоб взяти участь:</p>
        <p class="resultList">1. Увійдіть під своїм аккаунтом у Facebook.</p>
        <div onlogin="checkLoginState();" class="fb-login-button" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-scope="public_profile" data-auto-logout-link="false" data-use-continue-as="false"></div>
        <p class="resultList">2. Опублікуйте результат тесту на своїй сторінці.</p>
        `;
  }

  status.innerHTML = template;

  let fbLoginButton = document.querySelector('.fb-login-button');
  let resultList = status.querySelectorAll('.resultList');

  function statusChangeCallback(response) {
    if (response.status === 'connected') {
      if (!status.querySelector('.article_meta_media') && !isOn) {
        let div = document.createElement('div');
        div.className = 'article_meta_media';

        let p = document.createElement('p');
        p.classList = 'warrning-text';
        p.textContent = 'Обратите внимание: пост должен быть доступен для всех.';

        resultList[0].classList.add('active');
        fbLoginButton.classList.add('hide');
        status.appendChild(div);
        sharesDiv.addEventListener('click', function(e) {
          e.preventDefault();
          let elem = e.target;

          if (elem.classList.contains('btn-fb')) {
            const tempUglyHuck = context.quizApp._isCategories
              ? context.correctIndexItem
              : numberCorrect;
            const params = encodeURIComponent(
              'slug@' +
                context.quizApp._postSlug +
                '§game@' +
                context.quizApp._postSlug +
                '§count@' +
                tempUglyHuck +
                '§v@' +
                window.quiz.v
            );
            utils.Share.facebook(
              window.location.origin + window.location.pathname + '?fbresult=' + params
            );

            resultList[1].classList.add('active');
            status.appendChild(p);
            window.testAPI();
          }
        });

        div.appendChild(sharesDiv);
      }

      isOn = true;
    } else {
      window.fbAsyncInit();
      isOn = false;
    }
  }

  window.checkLoginState = function() {
    window.FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  };

  window.FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  window.testAPI = function() {
    fbLoginButton.classList.add('hide');

    window.FB.api('/me', function(response) {
      var oReq = new XMLHttpRequest();
      oReq.open(
        'POST',
        window.location.origin +
          '/wp-admin/admin-ajax.php?action=collect_lottery_users&slug=' +
          context.quizApp._postSlug,
        true
      );
      oReq.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
      oReq.send(JSON.stringify(response));
    });
  };
}

module.exports = StatusChangeCallback;
