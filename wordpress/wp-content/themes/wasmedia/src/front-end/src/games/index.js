const Games = function({ element }) {
  if (element.dataset['component'] === 'game') {
    let dir = 'index';

    if (element.dataset['type'] !== 'quiz') {
      dir = 'custom/' + element.dataset['type'] + '/' + dir;
    }

    const context = require.context('../quiz/', true, /\.js$/);
    const Game = context('./' + dir + '.js');
    new Game(element);
  }
};

module.exports = Games;
