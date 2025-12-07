const DEFAULT = require('./default');
const merge = require('webpack-merge');
const parts = require('./webpack.parts');

const commonConfig = merge([
  parts.entry(DEFAULT.MAIN.ENTRIES),
  parts.entry(DEFAULT.CUSTOM.ENTRIES),
  parts.output(),
  parts.loadTypeScript(),
  parts.loadImages(),
  parts.loadFonts(),
  parts.resolver(),
  parts.optimization(),
  parts.watch(),
  parts.generateSourceMaps('inline-source-map')
]);

const developmentConfig = merge([
  parts.output({
    library: '[name]'
  }),
  parts.loadJavaScript(),
  parts.loadSvg(),
  parts.loadCSS(),
  parts.devServer({
    domain: 'dev.was.media.lc',
    path: '../../../../../',
    hot: true,
    open: true,
    proxy: {
      '/wp-admin/admin-ajax.php': {
        target: 'http://dev.was.media.lc:80/wp-admin/admin-ajax.php'
      }
    }
  })
]);

const watchConfig = merge([
  parts.entry(DEFAULT.CACHE.ENTRIES),
  parts.clean([
    'public',
    '*cache*',
    'assets/js/*.build.js',
    'assets/js/*/*.build.js',
    'assets/css/*.build.css',
    'assets/css/*/*.build.css',
  ], '../../'),
  parts.output({
    filename: 'assets/js/[name].watch.js'
  }),
  parts.loadSvgIcon(),
  parts.loadAssets({
    path: '../../'
  }),
  parts.extractCSS({
    filename: '[name].watch',
    use: [{
      loader: 'css-loader'
    },
      'sass-loader']
  }),
]);

const productionConfig = merge([
  parts.clean([
    'public',
    '*cache*',
    'assets/js/*.watch.js',
    'assets/js/*/*.watch.js',
    'assets/js/*.build.js',
    'assets/js/*/*.build.js',
    'assets/css/*.watch.css',
    'assets/css/*/*.watch.css',
    'assets/css/*.build.css',
    'assets/css/*/*.build.css',
  ], '../../'),
  parts.entry(DEFAULT.CACHE.ENTRIES),
  parts.output({
    filename: 'assets/js/[name].[hash:8].build.js'
  }),
  parts.loadJavaScript({
    use: ['babel-loader', 'eslint-loader']
  }),
  parts.loadSvgIcon(),
  parts.watch(false),
  parts.loadAssets({
    path: '../../'
  }),
  parts.generateSourceMaps(false),
  parts.extractCSS({
    filename: '[name].[hash:8].build',
    use: [{
      loader: 'css-loader'
    },
      parts.autoprefix(),
      'sass-loader']
  }),
  parts.minifyCSS(),
  parts.minifyJavaScript(),
  parts.minifyImages(),
]);

module.exports = (env, {mode}) => {
  let config = '';

  const pages = DEFAULT.MAIN.PAGES.map(item => parts.page(item));

  switch (env) {
    case 'dev':
      config = merge(pages.concat([developmentConfig]));
      break;
    case 'watch':
      config = watchConfig;
      break;
    case 'prod':
      config = productionConfig;
      break;
    default:
      console.error(new Array(100).join('†'));
  }

  return merge([commonConfig, config, { mode }]);
};
