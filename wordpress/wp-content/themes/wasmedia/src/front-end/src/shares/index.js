function Shares({ elements, app = '' }) {
  let openShare = (e, elem) => {
    e.preventDefault();
    if (app) return false;

    windowPopup(elem.href, 500, 300);
  };

  let windowPopup = (url, width, height) => {
    let left = screen.width / 2 - width / 2,
      top = screen.height / 2 - height / 2;

    window.open(
      url,
      'displayWindow',
      'menubar=yes,toolbar=no,resizable=yes,scrollbars=yes,width=' +
        width +
        ',height=' +
        height +
        ',top=' +
        top +
        ',left=' +
        left
    );
  };

  elements.forEach(el => {
    el.addEventListener('click', function(e) {
      openShare(e, this);
    });
  });
}

module.exports = Shares;
