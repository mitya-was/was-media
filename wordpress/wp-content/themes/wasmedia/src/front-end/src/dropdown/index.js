function Dropdown({ elements, config, utils }) {
  let self = this;
  let db = config;
  let util = utils;

  elements.forEach((e, i) => {
    let dropdownOptions;

    if (e.dataset['config']) {
      dropdownOptions = db.initDropdown(e.dataset['config']);
      dropdownOptions['content'] = e.querySelector('.drop-inner');
    } else {
      dropdownOptions = db.initDropdown();
      let contentTooltips = e.closest('div').querySelectorAll('.drop-inner');
      [].forEach.call(contentTooltips, function(elem) {
        dropdownOptions['content'] = elem;
      });
      replacer(dropdownOptions);
    }

    dropdownOptions['target'] = e;

    self['drop_' + i] = new window.Drop(dropdownOptions);
  });

  function replacer(obj) {
    if (util.isMore(1025)) {
      obj['position'] = 'top center';
      obj['openOn'] = 'hover';
      return true;
    }
    obj['position'] = 'top left';
    obj['openOn'] = 'click';
  }
}

module.exports = Dropdown;
