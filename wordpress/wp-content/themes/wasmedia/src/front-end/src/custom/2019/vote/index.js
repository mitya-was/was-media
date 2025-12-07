import './styles/main.scss';

// const utils = require('utils');
// const fb_login = require('@/shares/fb');
// import Snippet from '@/images/Snippet';

const voteCandidates = document.getElementById('vote-candidates');
const voteElement = document.getElementById('vote2019');
//const voteControls = document.querySelector('.vote2019-control');
//const voteButton = document.querySelector('.vote-btn-js');
// const radios = document.querySelectorAll('.option-input');
// const fbLoginButton = document.querySelector('.fb-login-button');
const acc = document.getElementsByClassName('accordion');
let openedElement;
//let isVoted = false;

const modal = new window.tingle.modal({
  footer: false,
  stickyFooter: false,
  closeMethods: ['overlay', 'button', 'escape'],
  closeLabel: 'Назад',
  cssClass: ['vote-modal-theme'],
  onOpen() {
    Array.prototype.forEach.call(openedElement.querySelectorAll('.accordion'), item =>
      accordionOpen.call(item)
    );
    this.modal.scrollTop = openedElement.offsetTop;
  },
  onClose() {
    [...acc].forEach(item => accordionClose.call(item));
  }
});

modal.modalCloseBtnLabel.classList.add('btn');
modal.modalCloseBtnLabel.insertAdjacentHTML(
  'afterBegin',
  '<svg class="icon icon-angle icon-modal-close icon-sm" aria-hidden="true" role="img"><use href="#icon-angle-down" xlink:href="#icon-angle-down"></use></svg>'
);
modal.setContent(voteCandidates);

setInitialVote();

[...acc].forEach(item => item.addEventListener('click', accordionToggle.bind(item)));

/*window.checkLoginState = () => {
  window.FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
};*/

//fb_login();

function setInitialVote() {
  if (window.location.hash || /share=share/.test(window.location.href)) {
    let id = window.location.hash;
    openedElement = document.getElementById(id.split('#')[1]);

    if (openedElement) modal.open();

    window.history.replaceState('', null, window.location.origin + window.location.pathname + id);
  }
}

function accordionToggle() {
  let panel = this.nextElementSibling;
  this.classList.toggle('active');
  if (panel.style.maxHeight) {
    panel.style.maxHeight = null;
  } else {
    panel.style.maxHeight = panel.scrollHeight + 'px';
  }
}

function accordionOpen() {
  let panel = this.nextElementSibling;
  this.classList.add('active');
  panel.style.maxHeight = panel.scrollHeight + 'px';
}

function accordionClose() {
  let panel = this.nextElementSibling;
  this.classList.remove('active');
  panel.style.maxHeight = null;
}

function voteControlHandler(event) {
  const target = event.target;

  if (target.tagName === 'A') {
    event.preventDefault();
    let id = target.href.split('#')[1];
    openedElement = document.getElementById(id);
    modal.open();
  }

  /*if (target.tagName === 'INPUT') {
    if (isVoted) {
      event.preventDefault();
      return;
    }
    //target.parentElement.nextElementSibling.appendChild(voteControls);
    //voteControls.classList.remove('hide');
  }*/
}

/*function statusChangeCallback(response) {
  if (response.status === 'connected') {
    voteButton.onclick = voteRequest;
    voteButton.disabled = false;
    fbLoginButton.classList.add('hide');
  }
}*/

voteElement.addEventListener('click', voteControlHandler);

/*function createShareSnippet(element) {
  const snippetSrc = element.querySelector('.wp-post-image');
  const name = element.querySelector('.display-4').textContent.trim();
  let genderText;
  switch (name) {
    case 'Голда Меїр':
      genderText = 'найкраща президентка';
      break;
    default:
      genderText = 'найкращий президент';
      break;
  }

  const snippet = Snippet.createFacebookSnippet(snippetSrc);
  snippet.snippetUrlTxt = `${name} — ${genderText} в історії України`;

  const template = `<div class="vote-share-result">
    <img class="vote-snippet-preview" src="${snippet.snippetUrlTxt}" alt="" /> 
    <button id="fbshare" class="media-icons btn btn-fb btn-lg" type="button"><svg class="icon icon-xs" aria-hidden="true" role="img">
        <use href="#icon-fb" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-fb"></use>
    </svg> Поширити у Facebook</button>
</div>`;
  return template;
}*/

/*function voteRequest(event) {
  const target = event.target;
  const voteID = target.closest('.vote-item').dataset['id'];
  const message = document.createElement('span');
  message.className = 'h3';
  message.textContent = 'Дякуємо, ваш голос зараховано';
  fbLoginButton.insertAdjacentElement('afterend', message);
  message.insertAdjacentHTML('afterend', createShareSnippet(document.getElementById(voteID)));

  window.location.hash = voteID;

  const shareResultPreviewSrc = voteControls.querySelector('.vote-snippet-preview').src;
  radios.forEach(radio => (radio.disabled = true));
  target.closest('.vote-item').classList.add('voted');
  voteButton.remove();
  isVoted = true;

  const shareFbButton = document.getElementById('fbshare');

  shareFbButton.addEventListener('click', e => {
    if (e.target.tagName !== 'BUTTON') return;
    const params = 'share=' + encodeURIComponent(`share@picture§n@was§d@${shareResultPreviewSrc}`);
    utils.Share.facebook(
      `${window.location.origin}${window.location.pathname}?${params}${window.location.hash}`
    );
  });

  window.FB.api('/me?fields=id', function(response) {
    fetch(window.location.origin + '/wp-admin/admin-ajax.php?action=collect_elections_users', {
      method: 'POST',
      mode: 'cors',
      cache: 'default',
      body: JSON.stringify({
        user_id: response.id,
        vote: voteID
      })
    });
  });
}*/
