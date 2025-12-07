require('./style.scss');

module.exports = options => {
  options.tabs.forEach(tab => tab.addEventListener('click', tabHandler.bind(null, options)));
};

function tabHandler({ tabs, panes }, event) {
  event.preventDefault();

  tabs.forEach(tab => tab.classList.remove('active'));
  panes.forEach(panel => panel.classList.remove('active'));

  const anchor = event.target;
  const activePaneId = anchor.getAttribute('href');
  const activePane = document.querySelector(activePaneId);

  anchor.classList.add('active');
  activePane.classList.add('active');

  //TODO: UGLY HOOK current_post_type
  window.current_post_type = activePaneId.slice(1);
  window.not_in = window[activePaneId.slice(1) + '_not_in'];
}
