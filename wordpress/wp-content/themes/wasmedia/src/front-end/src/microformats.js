import './scss/microformats.scss';

const History = require('./history-url/index');
const ViewCounter = require('./viewcounter/index');
const util = require('./util/index');

let viewCounter = new ViewCounter({
  selector: 'article-main'
});

let scrollThrottled = util.throttle(viewCounter.updateCountView.bind(viewCounter, 150), 800);

document.addEventListener('scroll', scrollThrottled);

window.micro = new History({
  selector: 'article-main'
});
