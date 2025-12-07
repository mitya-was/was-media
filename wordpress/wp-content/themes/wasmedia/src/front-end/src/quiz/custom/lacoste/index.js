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

QuizApp.prototype.startQuiz = function () {
	this._isStarted = true;
	this.introView.toggle(true);
	this.outroView.toggle(true);
	this.questionView.toggle(false);

	const postId = document.querySelector('.article-main[data-postid]').dataset.postid;
	// const lang = document.querySelector('html').lang;
	// const title = document.querySelector('title').innerText;
	// const description = document.querySelector('meta[name="description"]')
	// 	? document.querySelector('meta[name="description"]').content : '';
  console.log(postId);
  this.nextQuestion(postId);
};

QuizApp.prototype.nextQuestion = function (postId) {
	let self = this;

	window.scrollTo(utils.getCoords({element: this._el, tOffset: -15}));

	window.gameData.postId = document.querySelector('.article-main').dataset.postid;

	let win = promotionWindow(
		`${window.location.origin}/static/${this._type}/build/?postId=${postId}`
	);
	let set = setInterval(function () {
		if (win && win.closed) {
			self.endQuiz();
			win = null;
			clearInterval(set);
		}
	}, 500);
};

QuizApp.prototype.endQuiz = function () {
	this._isStarted = false;
	this.questionView.toggle(true);
	this.outroView.toggle(false);
	this.outroView.displayOutroResult(1, 1);
};

module.exports = QuizApp;
