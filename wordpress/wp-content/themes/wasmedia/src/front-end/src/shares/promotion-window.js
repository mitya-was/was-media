function promotionWindow(url) {
  let windowObjectReference = null;

  if (windowObjectReference == null || windowObjectReference.closed) {
    windowObjectReference = window.open(
      url,
      '_blank',
    );
    windowObjectReference.focus();

    return windowObjectReference;
  }
}

module.exports = promotionWindow;
