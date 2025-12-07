"use strict";

function UI() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  return this.init(options);
}

UI.prototype.init = function() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  this.container = options.container;
  this.getComponents();
  return this;
};

UI.prototype.query = function() {
  var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";

  var response = [];
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = this.components[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var component = _step.value;

      if (component.dataset.component === name) response.push(component);
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator.return) {
        _iterator.return();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }

  return response.length === 1 ? response[0] : response;
};

UI.prototype.getComponents = function() {
  this.components = document.querySelectorAll("[data-component]");
  this.Spinner('play');
};

UI.prototype.Spinner = function() {
  var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();

  var self = this,
    spinner = this.query("spinner"),
    classList = spinner.classList,
    behavior = {
      spin: function spin() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.add("spin");
        actionCallback();
      },
      fade: function fade() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.add("fade");
        actionCallback();
      },
      show: function show() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.remove("fade");
        actionCallback();
      },
      freeze: function freeze() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.remove("spin");
        actionCallback();
      },
      close: function close() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.remove("open");
        actionCallback();
      },
      open: function open() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.add("open");
        actionCallback();
      },
      play: function play() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        self.Spinner('open').Spinner('spin');
        actionCallback();
      },
      stop: function stop() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        self.Spinner('fade', function() {
          return setTimeout(function() {
            self.Spinner('freeze').Spinner('close');
            actionCallback();
          }, 150);
        });
      },
      introduce: function introduce() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        self.Spinner('play', function() {
          return setTimeout(function() {
            self.Spinner('stop');
            actionCallback();
          }, 2250);
        });
      }
    };

  if (behavior.hasOwnProperty(action) && typeof behavior[action] === "function") {
    behavior[action](callback);
  }

  return this;
};

UI.prototype.Panelbox = function() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();
  var action = options.action;

  var self = this,
    panelbox = this.query("panelbox"),
    panels = {};
  var _arr = ['top', 'right', 'bottom', 'left'];

  var _loop = function _loop() {
    var key = _arr[_i];
    panels[key] = Array.from(panelbox.children).filter(function(item) {
      if (item.classList.contains(key)) return item;
    })[0];
  };

  for (var _i = 0; _i < _arr.length; _i++) {
    _loop();
  }
  var behavior = {
    open: function open() {
      var actionOptions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var actionCallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();
      var panel = actionOptions.panel;

      var mode = "",
        section = void 0,
        handle = void 0;
      if (panels.hasOwnProperty(panel)) {
        panel = panels[panel];
        if (!actionOptions.section || typeof actionOptions.section === "undefined" || actionOptions.section === "") {
          actionOptions.section = panel.querySelector('.panel-section');
        } else {
          actionOptions.section = panel.querySelector(".panel-section-" + actionOptions.section);
          if (!actionOptions.section) actionOptions.section = panel.querySelector('.panel-section');
        }
        if (!actionOptions.section) return;
        mode = !actionOptions.mode || typeof actionOptions.mode === "undefined" || actionOptions.mode === "" ? "profuse" : actionOptions.mode;
        section = actionOptions.section;
        handle = function handle() {
          self.Panelbox({
            action: 'showSection',
            panel: panel,
            section: section
          }, function() {
            panel.classList.add('open');
            actionCallback();
          });
        };
        mode === "avid" ? self.Panelbox({ action: "closeAll" }, handle) : handle();
      }
    },
    close: function close() {
      var actionOptions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var actionCallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();
      var panel = actionOptions.panel;

      if (panels.hasOwnProperty(panel)) {
        panel = panels[panel];
        panel.classList.remove('open');
        setTimeout(function() {
          self.Panelbox({
            action: "hideSection",
            panel: panel
          }, actionCallback);
        }, 250);
      }
    },
    showSection: function showSection() {
      var actionOptions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var actionCallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();
      var panel = actionOptions.panel,
        section = actionOptions.section;

      var sections = Array.from(panel.querySelectorAll('.panel-section'));
      sections.forEach(function(item, index) {
        item.classList.remove("show");
        if (index === sections.length - 1) {
          section.classList.add("show");
          actionCallback();
        }
      });
    },
    hideSection: function hideSection() {
      var actionOptions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var actionCallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();
      var panel = actionOptions.panel;

      var sections = Array.from(panel.querySelectorAll('.panel-section'));
      sections.forEach(function(item, index) {
        item.classList.remove("show");
        if (index === sections.length - 1) actionCallback();
      });
    },
    closeAll: function closeAll() {
      var actionOptions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var actionCallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();

      var panelKeys = Object.keys(panels);
      panelKeys.forEach(function(panel, index) {
        if (index === panelKeys.length - 1) {
          behavior.close({ panel: panel }, actionCallback);
        } else {
          behavior.close({ panel: panel });
        }
      });
    }
  };

  if (behavior.hasOwnProperty(action) && typeof behavior[action] === "function") {
    behavior[action](options, callback);
  }

  return this;
};

UI.prototype.Printbox = function() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '~';

  if (options === "~") return;

  var behavior = {
    print: function print() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";
      var mode = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "text";

      if (options === "~") return;
      var target = options.target,
        data = options.data;

      if (options.mode && typeof options.mode !== "undefined") mode = options.mode;
      if (mode === "text") {
        target.innerText = data;
      }
      if (mode === "html") {
        target.innerHTML = data;
      }
      if (mode === "attr") {
        var entries = Object.entries(data);
        target.setAttribute(entries[0][1], target.getAttribute([entries[0][1]]) + " " + entries[1][1]);
      }
    },
    clean: function clean() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";
      var mode = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "text";

      if (options === "~") return;
      var target = options.target,
        data = options.data;

      if (options.mode && typeof options.mode !== "undefined") mode = options.mode;
      if (mode === "text") {
        target.innerText = "";
      }
      if (mode === "html") {
        target.innerHTML = "";
      }
      if (mode === "attr") {
        var entries = Object.entries(data);
        target.setAttribute(entries[0][1], target.getAttribute([entries[0][1]]).replace(entries[1][1], "").trim());
      }
    }
  };

  if (arguments.length > 1) {
    Array.from(arguments).forEach(hit);
  } else {
    hit(options);
  }

  function hit() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";

    if (options === "~") return;
    var action = options.action;

    behavior[action](options);
  }

  return this;
};

UI.prototype.Modal = function() {
  var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();

  var spinner = this.query("modal"),
    classList = spinner.classList,
    behavior = {
      close: function close() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.remove("open");
        setTimeout(function() {
          classList.remove("expand");
          actionCallback();
        }, 200);
      },
      open: function open() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        classList.add("expand");
        classList.add("open");
        actionCallback();
      }
    };

  if (behavior.hasOwnProperty(action) && typeof behavior[action] === "function") {
    behavior[action](callback);
  }

  return this;
};

UI.prototype.Dropdown = function() {
  var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();

  var dropdown = this.query("dropdown"),
    classList = dropdown.classList,
    dropbody = dropdown.querySelector('.dropdown-body'),
    behavior = {
      close: function close() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        dropbody.style.height = "" + 0;
        classList.remove("open");
        actionCallback(this.calcHeight());
      },
      open: function open() {
        var actionCallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new Function();

        dropbody.style.height = this.calcHeight() + "px";
        classList.add("open");
        actionCallback(this.calcHeight());
      },
      calcHeight: function calcHeight() {
        return dropbody.children[0].offsetHeight + 32;
      }
    };

  if (behavior.hasOwnProperty(action) && typeof behavior[action] === "function") {
    behavior[action](callback);
  }

  return this;
};
