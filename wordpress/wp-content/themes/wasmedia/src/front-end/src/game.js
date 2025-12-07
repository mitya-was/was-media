require('./scss/game.scss');
const Games = require('./games/index');

const options = {
  element: document.querySelector('[data-component="game"]')
};

if (options.element) new Games(options);
