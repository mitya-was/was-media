const IntroView = require('../../intro');
const OutroView = require('../../outro');
const QuestionView = require('../../questionview');
const promotionWindow = require('../../../shares/promotion-window');
const utils = require('../../../util/index');

function QuizApp(element) {
  this._el = element;
  this._options = this._el.dataset['options'].split(',');
  this._type = this._el.dataset['type'];
  this.utils = utils;

  window.gameData = {};
  this._isStarted = false;

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

  this.nextQuestion();
};

QuizApp.prototype.nextQuestion = function() {
  let self = this;

  window.scrollTo(utils.getCoords({ element: this._el, tOffset: -15 }));

  window.gameData.role = 1;

  let win = promotionWindow(
    `https://was.media/wp-content/themes/wasmedia/src/front-end/src/custom/${this._type}/index.html`
  );
  let set = setInterval(function() {
    if (win && win.closed) {
      self.endQuiz();
      win = null;
      clearInterval(set);
    }
  }, 500);
};

QuizApp.prototype.endQuiz = function() {
  this._isStarted = false;
  this.questionView.toggle(true);
  this.outroView.toggle(false);
  this.outroView.displayOutroResult(window.gameData.role);
};

module.exports = QuizApp;
