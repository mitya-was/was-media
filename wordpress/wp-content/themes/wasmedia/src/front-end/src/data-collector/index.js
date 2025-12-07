function DataCollector() {
  let sid = genId();
  let appOptions = {};
  let blanksQty = 0;
  let appId = "";
  let err = { error: null };

  this.report = [];
  this.globalReport = err;
  this.tick = [];
  this.tickValue = 0;
  this.setTick = () => {
    this.destroyTick();
    if (!this.tick.length) {
      this.tick.push(setInterval(() => {
        this.tickValue += 100;
      }, 100));
    }
  };
  this.destroyTick = () => {
    clearTimeout(this.tick[0]);
    this.tick = [];
    this.tickValue = 0;
  };
  this.init = app => {
    appId = this.getGameId(document.body.classList) || "";
    appOptions.nowrong = (app._isScores !== 0) ? 1 : 0;

    this.app = app;
    this.statOptions = ["questions"];
    this.host = document.location.origin + "/";

    this.dataGetConfig = {
      pathName: this.createUrl('wp-admin/admin-ajax.php', {
        "action": "get_game_stats",
        "appId": appId,
        "statOptions": this.statOptions,
        "statType": appOptions.nowrong
      })
    };
    this.getData(this.dataGetConfig);
  };
  this.createBlank = obj => {
    this.item = new Blank({
      appId: appId,
      index: (obj.index - 1) || blanksQty,
      correctIndex: appOptions.nowrong ? -1 : obj.question.correctIndex
    });
    this.appLength = obj.appLength;
    this.setTick();
  };
  this.createUrl = (path, obj) => {
    let counter = 0;

    for (let key in obj) {
      let divider = counter === 0 ? "?" : "&";
      path += (divider + `${key}=${obj[key]}`);
      counter++;
    }

    return path;
  };
  this.hit = (index, value) => {

    if (this.item) {
      let config = {
        "chooseIndex": index,
        "time": this.tickValue,
        "win": appOptions.nowrong ? value : ((this.item.correctIndex === index) ? 1 : 0)
      };
      this.item.config(config, this.addHitReport);
    }
  };
  this.addHitReport = () => {
    this.report.push(this.item);
  };
  this.sendReport = (options, callback) => {
    callback = callback || new Function();
    let response = encodeReport(this.report);
    let request = new XMLHttpRequest();
    request.open('POST', this.host + options.pathName, true);
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
    request.send(response);
    request.onreadystatechange = () => {
      if (request.readyState === XMLHttpRequest.DONE) {
        this.getData(this.dataGetConfig);
      }
    };
    request.onerror = data => {
      this.getData(this.dataGetConfig);
    };
    callback();
    this.reset();
  };
  this.getData = (options, callback) => {
    callback = callback || new Function();
    let request = new XMLHttpRequest();
    request.open('GET', this.host + options.pathName, true);
    request.onreadystatechange = () => {
      if (request.readyState === XMLHttpRequest.DONE) {
        this.globalReport = request.responseText;
      }
    };
    request.onerror = data => {
      this.globalReport = err;
    };
    request.send();
    callback();
  };
  this.getGameId = (classes) => {
    let keyword = "postid-";
    let response = "";
    for (let i = 0; i < classes.length; i++) {
      if (~classes[i].indexOf(keyword)) {
        response = classes[i].replace(keyword, "");
      }
    }
    return response;
  };
  this.reSession = () => {
    sid = genId();
  };
  this.reset = () => {
    this.reSession();
    this.destroyTick();
    this.report = [];
    this.item = null;
  };

  function genId() {
    let response = "";

    for (let i = 0; i < 7; i++) {
      response += i === 0 ? stringifyDate() + "_" : _();
    }
    return response;
  }

  function stringifyDate() {
    let date = new Date();
    let mm = date.getMonth() + 1; // getMonth() is zero-based
    let dd = date.getDate();
    return [date.getFullYear(),
      (mm > 9 ? '' : '0') + mm,
      (dd > 9 ? '' : '0') + dd
    ].join().replace(/,/g, "");
  }

  function _() {
    return Math.random().toString(16).slice(2, 9);
  }

  function encodeReport(array) {
    let response = [];
    let matrix = [["index", "alpha"], ["correctIndex", "bravo"], ["chooseIndex", "charlie"], ["time", "delta"], ["win", "echo"]];
    for (let i = 0, item; i < array.length; i++) {
      item = array[i];
      let obj = {};
      for (let key in item) {
        for (let j = 0; j < matrix.length; j++) {
          if (key === matrix[j][0]) {
            obj[matrix[j][1] + "Index"] = item[key];
          }
          if (key === "appId" || key === "sessionId") {
            obj[key] = item[key];
          }
        }
      }
      response.push(obj);
    }
    return JSON.stringify(response);
  }

  function Blank(config) {
    this.appId = config.appId;
    this.sessionId = sid;
    this.index = config.index;
    this.correctIndex = appOptions.nowrong ? -1 : config.correctIndex;
    this.config = (obj, callback) => {
      callback = callback || new Function();
      for (let key in obj) {
        this[key] = obj[key];
      }
      callback(this);
    };
    return this;
  }

  return this;
}

module.exports = DataCollector;
