"use strict";

(function() {

  document.addEventListener("DOMContentLoaded", function() {
    var winOpener = window.opener ? window.opener : window;
    var location = window.opener ? winOpener.location.href : winOpener.document.location.href;
    //window.localStorage.setItem('gameData', 'none');
    var lang = /\/uk\//.test(location) ? "uk" : "ru";
    fetch("https://was.media/wp-content/themes/wasmedia/src/front-end/src/custom/gulliver/assets/json/data." + lang + ".json").then(function(response) {
      return response.json();
    }).then(function(response) {
      main(response.data, lang, winOpener);
    });
  });

  function main() {
    var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
    var lang = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "uk";
    var opener = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

    new Application({
      UI: new UI({
        container: document.querySelector(".UI")
      }),
      Playground: SVG('svg').size(window.innerWidth, window.innerHeight).panZoom({
        zoomMin: .25,
        zoomMax: 1.5,
        zoom: 1
      }),
      Data: data,
      lang: lang
    }, function(app) {
      return opener.gameData = app.exportGameData();
    });
  }
})();
