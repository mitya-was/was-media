/*
 * The IntroView
 *
 */
function IntroView(selector, quizApp) {
  this.element = quizApp._el.querySelector(selector);
  this.startButton = this.element.querySelector('button');
  this.quizApp = quizApp;
}

IntroView.prototype.attachEventHandlers = function() {
  if (~window.location.search.indexOf('fbresult')) {
    window.history.pushState('', '', window.location.origin + window.location.pathname + '');
  }

  this.startButton.addEventListener('click', this.quizApp.startQuiz.bind(this.quizApp));
};

IntroView.prototype.toggle = function(hide) {
  if (hide) {
    this.element.classList.add('hidden');
    this.quizApp._el.classList.remove('initGame');
  } else {
    this.element.classList.remove('hidden');
  }
};

module.exports = IntroView;
