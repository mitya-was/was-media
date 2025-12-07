import './style.scss';

/* TEMP USE ONLY */
import { template } from './template';

const stickyElement = document.querySelector('#post-49227 .entry-header, #post-52578 .entry-header');
stickyElement && stickyElement.insertAdjacentHTML('afterbegin', template);
/* ------------------- */

const targetTime = new Date('Oct 30, 2019 23:55:00').getTime();
const digitsList = [...document.querySelectorAll('.metro-clock .digit')];
const digitSegments = [
  [1, 2, 3, 4, 5, 6],
  [2, 3],
  [1, 2, 7, 5, 4],
  [1, 2, 7, 3, 4],
  [6, 7, 2, 3],
  [1, 6, 7, 3, 4],
  [1, 6, 5, 4, 3, 7],
  [1, 2, 3],
  [1, 2, 3, 4, 5, 6, 7],
  [1, 2, 7, 3, 6]
];

if (digitsList.length > 0) setInterval(renderClock, 1000);

function renderClock() {

  const diff = targetTime - new Date().getTime();

  if (diff >= 0) {
    const time = {
      days: Math.floor(diff / 86400000),
      hours: Math.floor((diff % 86400000) / 3600000),
      minutes: Math.floor((diff % 3600000) / 60000),
      seconds: Math.floor((diff % 60000) / 1000)
    };

    digitsList.forEach((item, i) => {
      let type = item.dataset['type'];
      const number = i % 2 === 0 ? Math.floor(time[type] / 10) : time[type] % 10;
      setNumber(item, number);
    });
  }
}

function toggleClass(digitSegment, index, segments) {
  setTimeout(() => segments[digitSegment - 1].classList.toggle('on'), index * 45);
}

function setNumber(digit, number) {
  const segments = digit.querySelectorAll('.segment');
  const current = parseInt(digit['timer'], 10);

  if (!isNaN(current) && current != number) {
    digitSegments[current].forEach((digitSegment, index) => toggleClass(digitSegment, index, segments));
  }

  if (isNaN(current) || current != number) {
    setTimeout(() => digitSegments[number].forEach((digitSegment, index) => {
      toggleClass(digitSegment, index, segments);
    }), 10);
    digit['timer'] = number;
  }
}
