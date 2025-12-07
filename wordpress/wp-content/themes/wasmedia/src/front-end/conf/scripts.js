const path = require('path');
const PKG = require('../package.json');
const GitRevisionPlugin = require('git-revision-webpack-plugin');
const _root = path.resolve(__dirname, '..');

const date = {
  day: new Date().getDate(),
  month: ('Jan Feb Mar Apr May June July Aug Sep Oct Nov Dec')
  .split(' ')[new Date().getMonth()],
  year: new Date().getFullYear(),
  time: new Date().getHours() + ':' + new Date().getMinutes()
};


module.exports = {
  banner: `Released: ${date.month} ${date.day},${date.time}\nEntry [[name]]#[hash:8]\n-----------------------------\n@git ${new GitRevisionPlugin().branch()} v${new GitRevisionPlugin().version()}\n-----------------------------
  ${PKG.name.toUpperCase()} ${PKG.version}
  ${PKG.description}
  ${PKG.homepage}
  ---------------------------
  © ${date.year}, ${PKG.author.email}
  @license ${PKG.license}`,

  logger(module) {
    return (...rest) => {
      const args = [module.filename].concat(rest);
      console.log.apply(console, args);
    }
  },

  root(...args) {
    return path.join.apply(path, [_root].concat(args));
  }
};
