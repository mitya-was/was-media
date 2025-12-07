/*
 * Single question object.
 * It has properties that reflect the data, and a set of methods to interact with that data.
 *
 * */
function Question(data, isMulti) {
  //let self = this;
  this.currentItem = data;
  this.question = data.querySelector('.question');
  this.versionContainer = data.querySelector('.versions');
  this.note = data.querySelector('.notes');
  this.versions = data.querySelectorAll('.versions button');

  for (let i = 0; i < this.versions.length; i++) {
    let hash = this.versions[i].dataset['hash'];

    if (!isMulti && this.isCorrect(hash)) this.correctIndex = i;
  }
}

Question.prototype.isCorrect = function(num) {
  // if (!/^\d+$/.test(num)) {
  //      return false;
  // }
  //
  // let result = num.slice(4, -1);
  //
  // return result % 3 === 0;

  const _0xeff2 = ['\x74\x65\x73\x74', '\x73\x6C\x69\x63\x65'];
  if (!/^\d+$/[_0xeff2[0]](num)) {
    return false;
  }
  const result = num[_0xeff2[1]](4, -1);
  return result % 3 === 0;
};

Question.prototype.checkAnswer = function(index) {
  return +index === +this.correctIndex;
};

Question.prototype.toggleNote = function(bool) {
  if (bool && this.note) {
    this.note.classList.add('hide');
  } else if (this.note) {
    this.note.classList.remove('hide');
  }
};

Question.prototype.checkSortedAnswers = function() {
  return [...this.versionContainer.children]
    .every((version, i) => Number(version.dataset.hash) === (i + 1))
}

Question.prototype.forEachAnswer = function(callback, context) {
  [].forEach.call(this.versions, callback, context);
};

module.exports = Question;
