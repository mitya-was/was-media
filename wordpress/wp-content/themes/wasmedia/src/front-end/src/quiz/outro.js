/*
 * The OutroView
 *
 */
const readMore = require('./read-more-button');
readMore();

const fbInit = require('../shares/fb');
const fb_login = require('../shares/fb_login.js');

function OutroView(selector, quizApp) {
  this.quizApp = quizApp;
  this.element = quizApp._el.querySelector(selector);
  this.resultTitle = this.element.querySelector('.result-title');
  this.resultResultImage = this.element.querySelector('.wp-post-image');
  this.outroMsg = this.element.querySelector('.outroMsg');
  this.pageTitle = document.querySelector('.entry-content .h-entry').textContent;

  this.restartButton = this.element.querySelector('button');
  this.resultDataSnippets = this.element.querySelectorAll('.gameOutro-snippets > div');
  this.lotteryTemplate = this.quizApp._isLottery
    ? this.element.querySelector('.gameOutro__lottery')
    : '';
  this.shr = document.querySelector('.article_meta_media');
  this.correctIndexItem = '';
  this.resultCounter = this.element.querySelector('.result-counter');

  this._isStarted = false;

  window.quiz = {
    slug: this.quizApp._postSlug,
    game: this.quizApp._postSlug,
    count: this.resultDataSnippets.length,
    v: this.quizApp._isCategories ? '2' : '1'
  };
}

OutroView.prototype.getCategoriesResult = function(numberCorrect) {
  let resultCats = numberCorrect.trim().split(' ');

  for (let i = 0; i < this.resultDataSnippets.length; i++) {
    let item = this.resultDataSnippets[i];

    let positiveArr = resultCats.filter(function(data) {
      return data === item.dataset['num'];
    });

    if (positiveArr.length > item.dataset['val']) {
      this.resultResultImage.src = item.dataset['snippets'];

      if (item.dataset['in'] === '1' && this.lotteryTemplate) {
        this.shr.classList.add('hide');
        this.lotteryTemplate.classList.remove('hide');
      } else if (this.lotteryTemplate) {
        this.shr.classList.remove('hide');
        this.lotteryTemplate.classList.add('hide');
      }

      this.correctIndexItem = i;

      break;
    }
  }
};

OutroView.prototype.getNumResult = function(numberCorrect) {
  let resultTextLength = this.resultDataSnippets.length;

  for (let i = 0; i < resultTextLength; i++) {
    if (numberCorrect > this.resultDataSnippets[i].dataset['num'] && i !== resultTextLength - 1) {
      continue;
    }

    this.correctResult = this.resultDataSnippets[i];

    this.resultResultImage.src = this.correctResult.dataset['snippets'];

    if (!this.quizApp._isStaticSnippetResult)
      this.resultTitle.innerHTML = this.resultDataSnippets[i].innerHTML;

    if (this.quizApp._isLottery || this.resultDataSnippets[i].dataset['in'] === '1') {
      this.lotteryTemplate.classList.remove('hide');
    }

    break;
  }
};

OutroView.prototype.getCustomResult = function() {
  let resultTextLength = this.resultDataSnippets.length;

  for (let i = 0; i < resultTextLength; i++) {
    if (
      window.gameData.points > this.resultDataSnippets[i].dataset['num'] &&
      i !== resultTextLength - 1
    )
      continue;

    this.resultTitle.innerHTML = this.resultDataSnippets[i].innerHTML;
    this.correctResult = this.resultDataSnippets[i];
    this.resultResultImage.src = this.correctResult.dataset['snippets'];

    break;
  }
};

OutroView.prototype.displayOutroResult = function(numberCorrect=0, totalQuestions=1) {
  if (this.quizApp._isLottery && numberCorrect >= 0) {
    fb_login(this, numberCorrect);
  }

  if (!this.quizApp._isStaticSnippetResult && !this.quizApp._isScores && !this.quizApp._isCustom) {
    this.resultCounter.textContent = `${numberCorrect} / ${totalQuestions}`;
  }

  if (!this.quizApp._isStaticSnippetResult && this.quizApp._isCustom) {
    if(totalQuestions === undefined) return;
    this.resultCounter.textContent = `${window.gameData.points} / ${window.gameData.totalPoints}`;
  }

  if (this.quizApp._isCustom) {
    this.getCustomResult(numberCorrect);
  } else if (this.quizApp._isCategories) {
    this.getCategoriesResult(numberCorrect);
  } else {
    this.getNumResult(numberCorrect, totalQuestions);
  }

  this.sharer = e => {
    e.preventDefault();
    let elem = e.target;

    if (elem.classList.contains('btn-tlg')) {
      this.quizApp.utils.Share.telegram(
        window.location.origin + window.location.pathname,
        this.resultTitle.textContent + ' - WAS'
      );
    }

    if (elem.classList.contains('btn-fb')) {
      let tempUglyHuck = this.quizApp._isCategories
        ? this.correctIndexItem
        : this.quizApp._isCustom
        ? window.gameData.points
        : numberCorrect;
      let params = encodeURIComponent(
        'slug@' +
          this.quizApp._postSlug +
          '§game@' +
          this.quizApp._postSlug +
          '§count@' +
          tempUglyHuck +
          '§v@' +
          window.quiz.v
      );
      this.quizApp.utils.Share.facebook(
        window.location.origin + window.location.pathname + '?fbresult=' + params
      );
    }

    if (elem.classList.contains('btn-wasted')) {
      let str = this.resultTitle.textContent.trim();
      this.quizApp.utils.Share.twitter(
        window.location.origin + window.location.pathname,
        str + ' - WAS'
      );
    }
  };
};

OutroView.prototype.attachEventHandlers = function() {
  this.createShares();

  fbInit();

  this.restartButton.addEventListener('click', this.quizApp.startQuiz.bind(this.quizApp));
};

OutroView.prototype.toggle = function(hide) {
  this.resultTitle.textContent = '';
  this.resultCounter.textContent = '';

  if (hide) {
    this.element.classList.add('hidden');
    this.sharesDiv.classList.add('toggler');
    this.social_text.classList.remove('hidden');
    this.quizApp._el.classList.remove('endPlay');
  } else {
    this.element.classList.remove('hidden');
    this.sharesDiv.classList.remove('toggler');
    this.social_text.classList.add('hidden');
    this.quizApp._el.classList.add('endPlay');
  }
};

OutroView.prototype.createShares = function() {
  let self = this;
  this.social_text = document.querySelector('.article_meta_media');
  this.sharesDiv = this.social_text.cloneNode(true);
  this.sharesDivLinks = [];

  this.sharesDiv.classList.remove('editor_default');
  this.sharesDiv.classList.remove('editor_offset');
  this.sharesDiv.classList.add('media_result');
  this.sharesDiv.classList.add('toggler');

  let text = document.createElement('p');
  text.className = 'article_meta_social-text';
  text.textContent =
    this.quizApp.utils.isLang() === 'ru' ? 'Поделиться результатом:' : 'Поділитися результатом:';
  this.sharesDiv.insertBefore(text, this.sharesDiv.children[0]);

  this.outroMsg.appendChild(this.sharesDiv);

  for (let i = 0; i < this.sharesDiv.children.length; i++) {
    if (this.sharesDiv.children[i].tagName !== 'A') {
      continue;
    }

    this.sharesDiv.children[i].href = '#';
    this.sharesDiv.children[i].classList.add('btn-brand');
    this.sharesDivLinks.push(this.sharesDiv.children[i]);
  }

  this.sharesDiv.addEventListener('click', function(e) {
    e.preventDefault();
    self.sharer(e);
  });
};

module.exports = OutroView;
