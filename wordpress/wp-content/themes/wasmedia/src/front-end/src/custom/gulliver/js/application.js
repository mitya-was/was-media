"use strict";

function Application() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";
  var callback = arguments[1];

  if (options === "~") return Object.create(null);
  return this.init(options, callback);
}

Application.prototype.init = function() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";
  var callback = arguments[1];

  if (options === "~") return Object.create(null);
  this.UI = options.UI;
  this.Playground = options.Playground;
  this.Data = options.Data;
  this.lang = options.lang;
  this.Stats = {
    zoomFactor: 1,
    inPlay: false,
    canHit: false,
    listenEvents: false,
    totalQuestionAmount: 0,
    questionCount: 0,
    level: {
      current: -1,
      currentQuestion: -1,
      currentQuestionProper: -1,
      amount: options.Data.length,
      questionAmount: 0,
      elements: this.Playground.group(),
      reset: function reset() {
        this.currentQuestion = -1;
        this.currentQuestionProper = -1;
      }
    },
    score: 0
  };
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = Object.keys(options.Data)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var key = _step.value;

      this.Stats.totalQuestionAmount += options.Data[key].quests.length;
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

  this.play();
  if (callback && typeof callback === "function") callback(this);
  return this;
};

Application.prototype.play = function() {
  var _this = this;

  if (this.Stats.inPlay === false) {
    this.Stats.inPlay = true;
    this.nextLevel(function() {
      return setTimeout(function() {
        return _this.UI.Spinner('stop');
      }, 1250);
    });
  }
};

Application.prototype.nextLevel = function() {
  var _this2 = this;

  var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";

  var levelStats = this.Stats.level;

  levelStats.reset();

  if (levelStats.current + 1 < levelStats.amount && this.Stats.questionCount < this.Stats.totalQuestionAmount) {
    levelStats.current += 1;

    levelStats.questionAmount = this.Data[levelStats.current].quests.length;
    if (levelStats.current > 0) {
      var members = this.Playground.select('.map-item').members;
      var _iteratorNormalCompletion2 = true;
      var _didIteratorError2 = false;
      var _iteratorError2 = undefined;

      try {
        for (var _iterator2 = members[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
          var item = _step2.value;
          item.remove();
        }
      } catch (err) {
        _didIteratorError2 = true;
        _iteratorError2 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion2 && _iterator2.return) {
            _iterator2.return();
          }
        } finally {
          if (_didIteratorError2) {
            throw _iteratorError2;
          }
        }
      }
    }

    this.Stats.fitScreenSize = this.calcCoverFitRatio(this.Data[levelStats.current].cover.height);

    var cover = this.Playground.image("https://was.media/wp-content/themes/wasmedia/src/front-end/src/custom/gulliver/assets/images/cover/" + levelStats.current + ".png", this.Data[levelStats.current].cover.width, this.Data[levelStats.current].cover.height).addClass('cover').addClass('map-item');
    var current = levelStats.current,
      cords = this.Data[current].targetCords,
      iterator = Array.from({ length: cords.length }, function(v, k) {
        return k;
      });
    var target = iterator.map(function(index) {
      return _this2.Playground.path(cords[index]).addClass('figure').addClass("figure-" + index).data('figure', index).addClass('map-item').fill('rgba(255, 123, 0, .35)');
    });

    var levelElements = this.Stats.level.elements;

    levelElements.add(cover);

    var _iteratorNormalCompletion3 = true;
    var _didIteratorError3 = false;
    var _iteratorError3 = undefined;

    try {
      for (var _iterator3 = iterator[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
        var index = _step3.value;
        levelElements.add(target[index]);
      }
    } catch (err) {
      _didIteratorError3 = true;
      _iteratorError3 = err;
    } finally {
      try {
        if (!_iteratorNormalCompletion3 && _iterator3.return) {
          _iterator3.return();
        }
      } finally {
        if (_didIteratorError3) {
          throw _iteratorError3;
        }
      }
    }

    if (this.Data[levelStats.current].streetLabels) {
      var _iteratorNormalCompletion4 = true;
      var _didIteratorError4 = false;
      var _iteratorError4 = undefined;

      try {

        for (var _iterator4 = this.Data[levelStats.current].streetLabels[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
          var label = _step4.value;

          levelElements.add(this.Playground.text(label.name).font({
            family: 'Roboto, sans-serif',
            size: 50,
            anchor: 'middle',
            fill: "#fff"
          }).move(label.x, label.y).rotate(label.angle).addClass('map-item'));
        }
      } catch (err) {
        _didIteratorError4 = true;
        _iteratorError4 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion4 && _iterator4.return) {
            _iterator4.return();
          }
        } finally {
          if (_didIteratorError4) {
            throw _iteratorError4;
          }
        }
      }
    }

    levelElements.draggable().on('beforedrag', function(event) {
      if (typeof event.detail.event.touches === "undefined" || event.detail.event.touches[0].target.classList.contains('figure')) {
        event.preventDefault();
      } else {
        levelElements.addClass('all');
      }
    }).on('dragend', function() {
      levelElements.removeClass('all');
    });

    var lastY = levelElements.y();
    if (levelStats.current === 0 && levelStats.currentQuestion <= 0) this.Playground.zoom(this.Stats.fitScreenSize);
    levelElements.cx(window.innerWidth / 2);
    if (levelStats.current === 0 && levelStats.currentQuestion <= 0) levelElements.y(lastY - lastY * this.Stats.fitScreenSize - 256);

    this.UI.Panelbox({
      action: "open",
      panel: "top",
      mode: "refuse"
    });

    this.usePanelByOrientation();

    this.nextQuest(function() {
      return callback !== "~" && callback();
    });
  } else {
    this.gameOver();
  }
};

Application.prototype.usePanelByOrientation = function() {
  var vw = window.innerWidth,
    vh = window.innerHeight;
  var condition = vw >= vh;
  this.Playground.size(vw, vh);
  this.UI.Panelbox({
    action: condition === true ? "open" : "close",
    panel: "left",
    mode: "refuse"
  });
  this.UI.Panelbox({
    action: condition === true ? "open" : "close",
    panel: "right",
    mode: "refuse"
  });
  this.UI.Panelbox({
    action: condition === true ? "close" : "open",
    panel: "bottom",
    mode: "refuse"
  });
};

Application.prototype.calcCoverFitRatio = function(height) {
  var maxHeight = window.innerHeight - document.querySelector('.panel-section-0').offsetHeight / 2 - 120;
  return height > maxHeight ? maxHeight / height : 1;
};

Application.prototype.nextQuest = function() {
  var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";


  var levelStats = this.Stats.level,
    answer = document.querySelector('.answer');

  this.listenEvents(true);

  answer.classList.remove('fail', 'success');

  this.UI.Printbox({
    target: answer.querySelector('.title'),
    action: 'clean'
  }, {
    target: answer.querySelector('.text'),
    action: 'clean'
  });

  if (levelStats.currentQuestion + 1 < levelStats.questionAmount) {
    levelStats.currentQuestion += 1;
    this.Stats.questionCount += 1;

    var currentQuestion = this.Data[levelStats.current].quests[levelStats.currentQuestion];
    var quest = currentQuestion.quest,
      proper = currentQuestion.proper;

    levelStats.currentQuestionProper = proper;

    this.UI.Printbox({
      target: document.querySelector('.question'),
      action: 'print',
      data: quest
    }, {
      target: document.querySelector('.counter'),
      action: 'print',
      data: this.Stats.questionCount + " / " + this.Stats.totalQuestionAmount
    });

    this.UI.Dropdown('open');

    if (this.Stats.canHit === false) this.Stats.canHit = true;

    callback !== "~" && callback();
  } else {
    this.nextLevel();
  }
  return this;
};

Application.prototype.gameOver = function() {

  this.Stats.inPlay = false;
  this.Stats.canHit = false;

  var gameData = this.exportGameData({
    from: 'gulliver',
    points: this.Stats.score,
    totalPoints: this.Stats.totalQuestionAmount
  });
  window.opener.gameData = gameData;
  setTimeout(function() {
    return window.close();
  }, 250);
  // if (window.opener) {
  //
  // } else {
  //     window.localStorage.setItem('gameData', JSON.stringify(gameData));
  // }

  return this;
};

Application.prototype.passQuestResult = function() {
  var index = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : -1;

  var condition = index === this.Stats.level.currentQuestionProper;
  var target = this.Playground.select(".figure-" + index);
  if (condition === true) {
    this.addScorePoint().showResponse(condition, target);
  } else {
    this.showResponse(condition, target, this.Playground.select(".figure-" + this.Stats.level.currentQuestionProper));
  }
  return condition;
};

Application.prototype.listener = function(event) {
  var _this3 = this;

  var stats = this.Stats;
  var target = event.target,
    targetClass = target.classList;
  var is = function is() {
    var condition = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new Function();

    if (condition === true) callback();
  };
  is(targetClass.contains("figure") && stats.canHit === true, function() {
    event.preventDefault();
    stats.canHit = false;
    var figureIndex = +target.dataset.figure;
    if (!isNaN(figureIndex)) {
      _this3.passQuestResult(figureIndex);
      stats.canHit = true;
    }
  });
  is(targetClass.contains("btn"), function() {
    event.preventDefault();
    is(targetClass.contains("zoom"), function() {
      is(targetClass.contains("zoom-in"), function() {
        if (_this3.Playground.zoom() < 1.5) {
          _this3.Playground.zoom(_this3.Playground.zoom() + .05);
        }
      });
      is(targetClass.contains("zoom-out"), function() {
        if (_this3.Playground.zoom() > .25) {
          _this3.Playground.zoom(_this3.Playground.zoom() - .05);
        }
      });
      is(targetClass.contains("zoom-default"), function() {
        _this3.Playground.zoom(_this3.Stats.fitScreenSize);
      });
    });
    is(targetClass.contains("next"), function() {
      var filled = _this3.Playground.select('.filled').members;
      var _iteratorNormalCompletion5 = true;
      var _didIteratorError5 = false;
      var _iteratorError5 = undefined;

      try {
        for (var _iterator5 = filled[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
          var item = _step5.value;
          _this3.refill(item);
        }
      } catch (err) {
        _didIteratorError5 = true;
        _iteratorError5 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion5 && _iterator5.return) {
            _iterator5.return();
          }
        } finally {
          if (_didIteratorError5) {
            throw _iteratorError5;
          }
        }
      }

      _this3.UI.Modal('close');
      _this3.nextQuest();
    });

    is(targetClass.contains("dropdown-trigger"), function() {
      var condition = target.parentNode.classList.contains('open');
      _this3.UI.Dropdown(condition === true ? 'close' : 'open');
    });
  });
};

Application.prototype.listenEvents = function() {
  var _this4 = this;

  var mode = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

  var stats = this.Stats;
  document.querySelector('.btn.next').innerText = this.lang === "uk" ? "Далі" : "Дальше";
  if (mode === true && stats.listenEvents === false) {
    stats.listenEvents = true;
    document.addEventListener('click', function(event) {
      return _this4.listener(event);
    });
    window.onbeforeunload = function() {
      window.opener && (window.opener.gameData = _this4.exportGameData({
        from: "gulliver",
        points: stats.score,
        totalPoints: stats.totalQuestionAmount
      }));
    };
    window.onresize = function() {
      _this4.usePanelByOrientation();
      _this4.Playground.size(window.innerWidth, window.innerHeight);
    };
  }
  if (mode === false && stats.listenEvents === true) {
    stats.listenEvents = false;
    document.removeEventListener('click', this.listener);
  }
};

Application.prototype.addScorePoint = function() {
  this.Stats.score += 1;
  return this;
};

Application.prototype.refill = function() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";
  var mode = arguments[1];

  if (target === "~") return;
  if (mode !== null && typeof mode !== "undefined") {
    target.addClass('filled').addClass(mode === true ? "success" : "fail");
  } else {
    target.removeClass('filled').removeClass('success').removeClass('fail');
  }
  return this;
};

Application.prototype.showResponse = function() {
  var mode = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "~";

  var _this5 = this;

  var target = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "~";
  var properTarget = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "~";

  if (mode === "~" || mode === true && target === "~" || mode === false && (target === "~" || properTarget === "~")) return;
  var self = this,
    answer = document.querySelector('.answer');
  var response = {
    title: {
      action: 'print',
      target: answer.querySelector('.title'),
      data: ""
    },
    text: {
      action: 'print',
      target: answer.querySelector('.text'),
      data: self.Data[self.Stats.level.current].quests[self.Stats.level.currentQuestion].response
    }
  };
  if (mode === true) {
    this.refill(target, true);
    setTimeout(function() {
      response.title.data = 'Правильно';
      hit(response);
    }, 1000);
  } else {
    this.refill(target, false);
    setTimeout(function() {
      _this5.refill(properTarget, true);
      setTimeout(function() {
        response.title.data = 'Неправильно';
        hit(response);
      }, 1000);
    }, 1250);
  }

  function hit(response) {
    var title = response.title,
      text = response.text;

    self.UI.Printbox(title);
    self.UI.Printbox(text);
    self.UI.Printbox({
      action: "print",
      target: answer,
      data: ["class", mode === true ? 'success' : 'fail'],
      mode: "attr"
    });
    self.UI.Modal("open");
  }
};

Application.prototype.exportGameData = function(data) {
  var response = {
    from: "gulliver",
    points: 0,
    totalPoints: 1
  };
  if (!data) return response;
  for (var key in data) {
    if (data.hasOwnProperty(key)) response[key] = data[key];
  }
  return response;
};
