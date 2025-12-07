const dropdownConfig = require('./dropdown/index.js');

exports.initDropdown = (name, config = null) => {
  config = dropdownConfig[name] ? dropdownConfig[name] : dropdownConfig['default'];
  return config;
};
