const Sortable = require('sortablejs/modular/sortable.core.esm');
/*
 * The QuestionView is where most of the action is.
 *
 */
function QuestionView(selector, quizApp) {
  this.element = quizApp._el.querySelector(selector);
  this.quizApp = quizApp;
  this.answersContainer = this.element.querySelectorAll('.versions');
  this.questionControl = this.element.querySelector('.gameControls');
  this.iframesYT = this.element.querySelectorAll('iframe[src*=youtube]');
  this.chooseClass = !this.quizApp._isMultiChoice ? 'btn-success' : 'btn-inverse';

  if (this.iframesYT.length > 0) {
    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
  }
}

QuestionView.prototype.isDruggableQuestion = function(question) {
  if (!question) return false;
  const className = 'versions-druggable';
  const element = typeof question === 'number' ? this.answersContainer[question] : question;
  return element.classList.contains(className);
}

QuestionView.prototype.noteDruggableToggler = function() {
  [...this.quizApp.quiz.currentQuestion.versionContainer.children]
    .forEach((version, i) => {
      if (Number(version.dataset.hash) === i + 1) {
        version.classList.add(this.chooseClass)
      } else {
        version.classList.add('btn-danger');
      }
    });
  this.quizApp.quiz.currentQuestion.toggleNote(false);
}

QuestionView.prototype.addAdminPaging = function() {
  const template = createElements`<ul class="pagination quiz-paging_admin">${this.answersContainer.length}</ul>`;
  this.element.insertAdjacentHTML('afterBegin', template);

  const paging = this.quizApp._el.querySelector('.quiz-paging_admin');

  paging.addEventListener('click', questionPaging.bind(this));

  function questionPaging(e) {
    e.preventDefault();

    if (e.target.tagName === 'A') {
      if (e.target.dataset['num'] !== 'result') {
        this.setQuestion(this.quizApp.quiz.questions[e.target.dataset['num']]);
        this.toggle(false);
        this.quizApp.outroView.toggle(true);
      } else {
        this.toggle(true);
        this.quizApp.outroView.toggle(false);
        this.quizApp.outroView.displayOutroResult(
          this.quizApp.quiz.numberCorrect,
          this.quizApp.quiz.questions.length
        );
      }

      this.questionControl.innerHTML = e.target.textContent + '/' + this.answersContainer.length;
    }
  }

  function createElements(str, queue) {
    let count = 1,
      result =
        '<li class="page-item"><a class="page-link" data-num="result" href="#">Result</a></li>';

    while (queue > 0) {
      result = `${result} <li class="page-item"><a class="page-link" data-num="${count -
        1}" href="#">${count}</a></li>`;
      count++;
      queue--;
    }
    return str[0] + result + str[1];
  }
};

QuestionView.prototype.attachEventHandlers = function() {
  let self = this;
  let answer;
  let score;
  let flag = true;
  let isChosen = false;
  let sortableAnswear;
  self.questionTimeout = 0;
  self.tempTime = 0;
  let timeOut = self.quizApp._isScores ? 600 : 1000;

  setTimeout(() => {
    this.questionVideos = [...this.iframesYT]
      .map(video => {
        if (window.YT && window.YT.Player) {
          return new window.YT.Player(video);
        }
      })
      .filter(item => typeof item !== 'undefined');
  }, 1000);

  if (self.quizApp._isAfterClick) {
    self.questionBtnNext = self.element.querySelector('.item-control.btn-next');
    self.questionBtnNext.onclick = () => {
      const isDruggable = this.isDruggableQuestion(this.quizApp.quiz.currentQuestion.versionContainer);

      if (isDruggable && isChosen || !isDruggable) {
        this.quizApp.nextQuestion(answer, score);
        isChosen = false;
        if (sortableAnswear) sortableAnswear.option('disabled', false);
        flag = true;
      } else {
        if(self.quizApp.utils.isLang() == 'uk'){
          self.questionBtnNext.textContent = 'Далі';
        } else {
          self.questionBtnNext.textContent = 'Далее';
        }
        isChosen = isDruggable && !isChosen ? true : false;
        const isCorrect = self.quizApp.quiz.currentQuestion.checkSortedAnswers()
        if (isCorrect) score += 1;
        score = 0;
        if (isCorrect) score = 1;
        sortableAnswear.option('disabled', true);
        this.noteDruggableToggler();
      }
    };
  }

  for (let i = 0; i < this.answersContainer.length; i++) {
    const answearNode = this.answersContainer[i];

    if (this.isDruggableQuestion(answearNode)) {
      sortableAnswear = Sortable.Sortable.create(answearNode, {
        animation: 200,
      });
      score = 0;
      continue;
    }

    answearNode.onclick = function(event) {
      if (event.target.tagName !== 'BUTTON') {
        return false;
      }

      if (self.tempTime !== self.questionTimeout) {
        return false;
      }

      if (self.questionTimeout) {
        clearTimeout(self.questionTimeout);
      }

      answer = +event.target.dataset['item'];
      score = !self.quizApp._isCategories
        ? Number(event.target.dataset.hash)
        : ' ' + event.target.dataset.hash;

      if (flag) {
        self.noteToggler(event.target, answer);
        flag = false;
      }

      self.questionTimeout = setTimeout(function() {
        self.tempTime = self.questionTimeout;

        if (self.quizApp._isAfterClick) {
          self.questionBtnNext.classList.remove('hide');
          self.quizApp.quiz.questions[i].toggleNote(false);
          return false;
        }

        self.quizApp.nextQuestion(answer, score);
        flag = true;
      }, timeOut);
    };
  }
};

QuestionView.prototype.noteToggler = function(target, answer) {
  let self = this;
  let quiz = this.quizApp.quiz;

  if (self.quizApp._isMultiChoice || quiz.currentQuestion.checkAnswer(answer)) {
    target.classList.add(this.chooseClass);
  } else {
    target.classList.add('btn-danger');

    setTimeout(function() {
      //if(!quiz.currentQuestion.checkAnswer(answer)) return false;
      self.answersContainer[quiz.counter - 1].children[
        quiz.currentQuestion.correctIndex
      ].classList.add(self.chooseClass);
    }, 300);
  }
};

QuestionView.prototype.stopVideoQuestion = function() {
  if (Array.isArray(this.questionVideos)) {
    this.questionVideos.forEach(video => {
      if (
        typeof video !== 'undefined' &&
        video.stopVideo &&
        typeof video.stopVideo === 'function'
      ) {
        video.stopVideo();
      }
    });
  }
};

QuestionView.prototype.setQuestion = function(question) {
  let self = this;
  let items = this.quizApp.questionView.element.children;

  // self.quizApp.dataCollector.createBlank({
  //     index: this.quizApp.quiz.counter,
  //     question: question,
  //     appLength: this.quizApp.quiz.questions.length
  // });

  this.questionControl.innerHTML =
    this.quizApp.quiz.counter + '/' + this.quizApp.quiz.questions.length;

  for (let i = 0; i < items.length; i++) {
    items[i].classList.remove('gameItem__active');
  }

  question.forEachAnswer(function(answer) {
    answer.classList.remove(self.chooseClass);
    answer.classList.remove('btn-danger');
  });

  question.currentItem.classList.add('gameItem__active');

  if (this.quizApp._isAfterClick) {
    this.questionBtnNext.classList.add('hide');
  }

  if(this.isDruggableQuestion(question.versionContainer)) {
    this.quizApp._el.classList.add('__afterChoose');
    this.questionBtnNext.classList.remove('hide');
    if(this.quizApp.utils.isLang() == 'uk'){
      this.questionBtnNext.textContent = 'Перевірити';
    } else {
      this.questionBtnNext.textContent = 'Проверить';
    }
  }
};

QuestionView.prototype.toggle = function(hide) {
  if (hide) {
    this.quizApp._el.classList.remove('progressGame');
    this.element.classList.add('hidden');
  } else {
    this.quizApp._el.classList.add('progressGame');
    this.element.classList.remove('hidden');
  }
};

module.exports = QuestionView;
