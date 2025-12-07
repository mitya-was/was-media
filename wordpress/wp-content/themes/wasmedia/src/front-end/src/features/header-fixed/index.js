const html = document.documentElement,
  jewerly = document.querySelector('.jewelry-head'),
  offset = jewerly && jewerly.children.length > 0 ? 130 : 0;


module.exports = () => {
  if ((window.pageYOffset || document.documentElement.scrollTop) >= offset) {
    html.classList.add('header-stick');
  } else {
    html.classList.remove('header-stick');
  }
};
