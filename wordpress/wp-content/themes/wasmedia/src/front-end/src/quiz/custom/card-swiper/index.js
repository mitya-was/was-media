const IntroView = require('../../intro');
const OutroView = require('../../outro');
const QuestionView = require('../../questionview');
const utils = require('../../../util/index');
const helperList = require('./helper-list');

function QuizApp(element) {
  this._el = element;
  this._options = this._el.dataset['options'].split(',');
  this._type = this._el.dataset['type'];
  this.utils = utils;

  window.gameData = {};

  this._isStarted = false;

  const self = this;


  window.gameData = {
    modal: new window.tingle.modal({
      footer: false,
      stickyFooter: false,
      closeMethods: ['overlay'],
      closeLabel: '',
      cssClass: ['quiz-modal'],
      onClose() {
        self.endQuiz();
        window.gameData.modal.setContent();
      }
    })
  };

  let urlPathMask = window.location.pathname.split('/');
  this._postSlug = urlPathMask[urlPathMask.length - 2];

  this._isAfterClick = ~this._options.indexOf('notesafteranswer');
  this._isLottery = ~this._options.indexOf('lottery');
  this._isScores = ~this._options.indexOf('scores');
  this._isCustom = ~this._options.indexOf('custom');
  this._isCategories = ~this._options.indexOf('categories');
  this._isStaticSnippetResult = ~this._options.indexOf('staticSnippetResult');
  this._isMultiChoice = this._isScores || this._isCategories;

  this.introView = new IntroView('.gameIntro', this);
  this.outroView = new OutroView('.gameOutro', this);
  this.questionView = new QuestionView('.gameWrapper', this);

  this.introView.attachEventHandlers();
  this.outroView.attachEventHandlers();
  this.questionView.attachEventHandlers();
}

QuizApp.prototype.startQuiz = function() {
  this._isStarted = true;
  this.introView.toggle(true);
  this.outroView.toggle(true);
  this.questionView.toggle(false);
  window.gameData.points = 0;
  window.gameData.from = '';

  this.nextQuestion();
};

QuizApp.prototype.nextQuestion = function() {

  window.scrollTo(utils.getCoords({ element: this._el, tOffset: -15 }));
  window.gameData.postId = document.querySelector('.article-main').dataset.postid;

  window.gameData.modal.setContent(
      `<iframe class="iframeContainer iframe-fullsize" src='${window.location.origin}/static/${this._type}/build/index.html' />`
  );

  window.gameData.modal.open();
};

QuizApp.prototype.endQuiz = function() {
  this._isStarted = false;
  this.questionView.toggle(true);
  this.outroView.toggle(false);
  this.outroView.displayOutroResult(window.gameData.points);

  helperList(require(
      utils.isLang() === 'ru'
        ? './answers-ru.json'
        : './answers-uk.json'
    ));
};

module.exports = QuizApp;
