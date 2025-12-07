const path = require('path'),
  fs = require('fs'),
  webpack = require('webpack'),
  cssnano = require('cssnano'),
  scripts = require('./scripts'),
  PKG = require('../package.json'),
  templates = require('./templates'),
  autoprefixer = require('autoprefixer'),
  SassLintPlugin = require('sass-lint-webpack'),
  AssetsPlugin = require('assets-webpack-plugin'),
  WorkboxPlugin = require('workbox-webpack-plugin'),
  HtmlWebpackPlugin = require('html-webpack-plugin'),
  WebpackPwaManifest = require('webpack-pwa-manifest'),
  CleanWebpackPlugin = require('clean-webpack-plugin'),
  UglifyWebpackPlugin = require('uglifyjs-webpack-plugin'),
  PrettierPlugin = require('prettier-webpack-plugin'),
  ExtractTextPlugin = require('extract-text-webpack-plugin'),
  ImageminPlugin = require('imagemin-webpack-plugin').default,
  ScriptExtHtmlWebpackPlugin = require('script-ext-html-webpack-plugin'),
  OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const THEME_DEFAULT_PATH_IMAGES = '/wp-content/themes/wasmedia/assets/images/';

exports.entry = ({ ...path }) => ({
  entry: path
});

exports.output = ({ filename = 'assets/js/[name].js', path = '../../', library = '' } = {}) => ({
  output: {
    path: scripts.root(path),
    publicPath: '/',
    filename: (chunkData) => {
      if (/critical|cache/.test(chunkData.chunk.name)) {
        return 'assets/js/[name].js';
      } else {
        return filename;
      }
    },
    library
  }
});

exports.resolver = () => ({
  resolve: {
    extensions: ['.js', '.scss'],
    alias: {
      root: scripts.root('/'),
      '@': scripts.root(PKG.directories.src),
      components: scripts.root(PKG.directories.src + '/components'),
      utils: scripts.root(PKG.directories.src + '/util'),
      styles: scripts.root(PKG.directories.src + '/scss'),
    }
  }
});

exports.loadJavaScript = ({ use = [] } = {}) => {
  return {
    module: {
      rules: [
        {
          test: /\.js$/,
          include: scripts.root(PKG.directories.src),
          exclude: /node_modules/,
          use: [].concat(use)
        }
      ]
    }
  };
};

exports.loadTypeScript = () => ({
  module: {
    rules: [
      {
        test: /\.ts?$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'ts-loader',
            options: {
              context: __dirname,
              configFile: 'tsconfig.json'
            },
          }
        ]
      }
    ]
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js']
  },
});

exports.loadCSS = () => ({
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: ['style-loader', 'css-loader?sourceMap', 'sass-loader?sourceMap']
      }
    ]
  }
});

exports.loadImages = () => ({
  module: {
    rules: [
      {
        test: /\.(png|gif|jpe?g)$/i,
        use: {
          loader: 'file-loader',
          options: {
            name: '[path][name].[ext]',
            outputPath: '/'
          }
        }
      }
    ]
  }
});

exports.loadFonts = () => ({
  module: {
    rules: [
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        use: {
          loader: 'file-loader',
          options: {
            name: '[path][name].[ext]',
            publicPath: '/wp-content/themes/wasmedia/'
          }
        }
      }
    ]
  }
});

exports.loadSvgIcon = () => ({
  module: {
    rules: [
      {
        test: /\.svg$/,
        use: {
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'assets/images/'
          }
        }
      }
    ]
  }
});

exports.loadSvg = ({name = '[path][name].[ext]', outputPath = 'assets/images/', publicPath = THEME_DEFAULT_PATH_IMAGES} = {}) => ({
  module: {
    rules: [
      {
        test: /\.svg$/,
        exclude: /\.(sprite|inline)./i,
        use: {
          loader: 'file-loader',
          options: {
            name,
            publicPath,
            outputPath
          }
        }
      },
      {
        test: /\.svg$/,
        include: /\.inline./i,
        use: {
          loader: 'svg-inline-loader',
          options: {
            removeTags: true,
            removingTags: ['title', 'desc', 'class']
          }
        }
      }
    ]
  }
});

exports.loadPWA = ({ publicPath, favicon = `${PKG.directories.example}/favicon.png` } = {}) => ({
  plugins: [
    new WebpackPwaManifest({
      name: `${PKG.name}`,
      short_name: PKG.name,
      description: PKG.description,
      background_color: '#ff7b00',
      crossorigin: 'use-credentials', //can be null, use-credentials or anonymous
      fingerprints: false,
      publicPath,
      inject: true,
      ios: true,
      icons: [
        {
          src: scripts.root(favicon),
          sizes: [120, 152, 167, 180],
          destination: path.join('favicons', 'ios'),
          ios: true
        },
        {
          src: scripts.root(favicon),
          sizes: [36, 48, 72, 96, 144, 192, 256],
          destination: path.join('favicons', 'android')
        },
        {
          src: scripts.root(favicon),
          sizes: [16, 32],
          destination: '/'
        }
      ]
    })
  ]
});

exports.loadAssets = ({ path }) => {
  return {
    plugins: [
      new AssetsPlugin({
        filename: 'assets.json',
        path: scripts.root(path),
        fileTypes: ['js', 'css'],
        prettyPrint: true
      })
    ]
  }
};

exports.extractCSS = ({ filename, use = [] }) => {

  const plugin = new ExtractTextPlugin({
    filename: (getPath) => {
      if (/critical/.test(getPath('[name]'))) {
        return getPath('style.css');
      }
      if (/cache/.test(getPath('[name]'))) {
        return getPath('assets/css/[name].css');
      } else if (/dev/.test(getPath('[name]'))) {
        return getPath('assets/css/[name].dev.css');
      } else {
        return getPath(`assets/css/${filename}.css`);
      }
    },
    allChunks: true
  });

  return {
    module: {
      rules: [
        {
          test: /\.(sa|sc|c)ss$/,
          use: ExtractTextPlugin.extract({
            fallback: 'style-loader',
            use: use
          })
        }
      ]
    },
    plugins: [plugin]
  };
};

exports.autoprefix = () => ({
  loader: 'postcss-loader',
  options: {
    plugins: () => [autoprefixer()]
  }
});

exports.optimization = () => ({
  optimization: {
    noEmitOnErrors: true
  }
});

exports.minifyImages = () => ({
  plugins: [
    new ImageminPlugin({
      test: /\.(jpe?g|png|gif|svg)$/i,
      optipng: {
        optimizationLevel: 8
      }
    })
  ]
});

exports.minifyJavaScript = () => ({
  optimization: {
    minimizer: [new UglifyWebpackPlugin({
      sourceMap: false
    })]
  }
});

exports.minifyCSS = () => ({
  plugins: [
    new OptimizeCSSAssetsPlugin({
      cssProcessor: cssnano,
      cssProcessorOptions: {
        options: {
          discardComments: {
            removeAll: true
          },
          safe: false
        }
      },
      canPrint: false
    })
  ]
});

exports.generateSourceMaps = (bool) => ({
  devtool: bool
});

exports.attachBannerRevision = () => ({
  plugins: [
    new webpack.BannerPlugin(scripts['banner'])
  ]
});

exports.watch = (bool = true) => ({
  watch: bool
});

exports.clean = (files, path) => ({
  plugins: [
    new CleanWebpackPlugin(
      files,
      {
        root: scripts.root(path),
        verbose: false,
        dry: false,
        watch: false
      }
    )
  ]
});

exports.prettier = () => ({
  plugins: [
    new PrettierPlugin({
      charset: 'utf-8',
      printWidth: 100,
      tabWidth: 2,
      useTabs: false,
      semi: true,
      singleQuote: true,
      bracketSpacing: true,
      trailingCommas: 'all'
    })
  ]
});

exports.devServer = ({domain = 'localhost', path, proxy, hot, useLocalIp = true, open, port = 8888}) => ({
  devServer: {
    contentBase: scripts.root(path),
    public: `${domain}:${port}`,
    headers: { 'X-Custom-Header': 'yes' },
    useLocalIp,
    proxy,
    port,
    hot,
    open,
    https: {
      key: fs.readFileSync('./localhost.key'),
      cert: fs.readFileSync('./localhost.cert')
    },
    stats: 'errors-only',
    host: '0.0.0.0',
    overlay: true,
    watchOptions: {
      aggregateTimeout: 300,
      poll: 1000
    },
  },
  plugins: [
    new webpack.WatchIgnorePlugin([
      scripts.root('node_modules')
    ]),
    new webpack.HotModuleReplacementPlugin()
  ]
});

exports.sassLint = () => ({
  plugins: [
    new SassLintPlugin()
  ]
});

exports.workBox = () => ({
  plugins: [
    new WorkboxPlugin.GenerateSW({
      clientsClaim: true,
      skipWaiting: true
    })
  ]
});

exports.page = ({
                  chunks = [],
                  title = PKG.name,
                  slug = 'index',
                  ext = 'html',
                  template = require.resolve('html-webpack-plugin/default_index.ejs'),
                  bodyClass = 'post-template-default single isMobile',
                  bodyHtmlSnippet = '',
                  headHtmlSnippet = '',
                  options = 'default'
                } = {}) => {
  return {
    plugins: [
      new HtmlWebpackPlugin({
        chunks,
        title,
        filename: `${slug}.${ext}`,
        template: `!!ejs-webpack-loader!test/${template}.ejs`,
        headHtmlSnippet,
        bodyClass,
        bodyHtmlSnippet,
        ...templates[options]
      }),
      new ScriptExtHtmlWebpackPlugin({
        defaultAttribute: 'defer'
      })
    ]
  }
};
