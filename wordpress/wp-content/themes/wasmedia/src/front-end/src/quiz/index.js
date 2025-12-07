const Quiz = require('./quiz'),
  IntroView = require('./intro'),
  OutroView = require('./outro'),
  QuestionView = require('./questionview'),
  utils = require('../util/index');
//dataCollector = require('../data-collector');

class QuizApp {
  constructor(element) {
    this._el = element;
    this._options = this._el.dataset['options'].split(',');
    this.utils = utils;

    let urlPathMask = window.location.pathname.split('/');
    this._postSlug = urlPathMask[urlPathMask.length - 2];

    this._isAfterClick = ~this._options.indexOf('notesafteranswer');
    this._isCustom = ~this._options.indexOf('custom');
    this._isScores = ~this._options.indexOf('scores');
    this._isStaticSnippetResult = ~this._options.indexOf('staticSnippetResult');
    this._isLottery = ~this._options.indexOf('lottery');
    this._isCategories = ~this._options.indexOf('categories');
    this._isMultiChoice = this._isScores || this._isCategories;

    this._isStarted = false;

    //this.dataCollector = new dataCollector();

    this.introView = new IntroView('.gameIntro', this);
    this.outroView = new OutroView('.gameOutro', this);
    this.questionView = new QuestionView('.gameWrapper', this);

    this.introView.attachEventHandlers();
    this.outroView.attachEventHandlers();
    this.questionView.attachEventHandlers();

    if (utils.isLoggedIn()) this.questionView.addAdminPaging();
  }

  startQuiz() {
    this.quiz = new Quiz('.gameItem', this);

    this.introView.toggle(true);
    this.outroView.toggle(true);
    this.questionView.toggle(false);
    this._isStarted = true;

    //this.dataCollector.init(this);

    this.nextQuestion();
  }

  nextQuestion(answer, score) {
    const nextQuestion = this.quiz.advanceQuestion(answer, score);

    //this.dataCollector.hit(answer, score);

    window.scrollTo(utils.getCoords({ element: this._el, tOffset: -15 }));

    window.dataLayer &&
      window.dataLayer.push({
        event: 'quizNextQuestion',
        eventCategory: 'Quiz',
        eventAction: 'click',
        eventCounter: this.quiz.counter,
        eventLabel: this._postSlug
      });

    if (nextQuestion) {
      this.questionView.setQuestion(nextQuestion);
    } else {
      this.endQuiz();
    }

    this.questionView.stopVideoQuestion();
  }

  endQuiz() {
    // this.dataCollector.sendReport({
    //     pathName: 'wp-admin/admin-ajax.php?action=collect_game_stats&statType=' + this.dataCollector.statType
    // });
    this._isStarted = false;
    this.questionView.toggle(true);
    this.outroView.toggle(false);
    this.outroView.displayOutroResult(this.quiz.numberCorrect, this.quiz.questions.length);
  }
}

module.exports = QuizApp;
