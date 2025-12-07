/*
 * Quiz object.
 *
 * */
let Question = require('./question');

function Quiz(selector, quizApp) {
  this.numberCorrect = !quizApp._isCategories ? 0 : '';
  this.counter = 0;
  this.questions = [];
  this.quizApp = quizApp;

  this.addQuestions(quizApp._el.querySelectorAll(selector));
}

Quiz.prototype.addQuestions = function(data) {
  for (let i = 0; i < data.length; i++) {
    let q = new Question(data[i], this.quizApp._isMultiChoice);

    if (this.quizApp._isAfterClick) {
      q.toggleNote(true);
    }

    this.questions.push(q);
  }
};

Quiz.prototype.advanceQuestion = function(lastAnswer, score) {
  if (this.currentQuestion) {
    if (!this.quizApp._isMultiChoice && !this.quizApp.questionView.isDruggableQuestion(this.currentQuestion.versionContainer) && this.currentQuestion.checkAnswer(lastAnswer)) {
      this.numberCorrect++;
    }

    if (this.quizApp._isMultiChoice || this.quizApp.questionView.isDruggableQuestion(this.currentQuestion.versionContainer)) {
      this.numberCorrect += score;
    }
  }

  this.currentQuestion = this.questions[this.counter++];

  return this.currentQuestion;
}

module.exports = Quiz;
