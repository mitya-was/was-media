module.exports = {
  MAIN: {
    ENTRIES: {
      critical: './src/critical.js',
      main: './src',
      single: './src/single.js',
      archive: './src/archive.js',
      home: './src/home.js',
      game: './src/game.js',
      newsletter: './src/newsletter.js',
      microformats: './src/microformats.js',
    },
    PAGES: [
      {
        chunks: ['home', 'main', 'critical'],
        title: 'WAS - Home',
        template: 'home',
        slug: 'home',
        bodyClass: 'home blog default-theme',
        bodyHtmlSnippet: 'Home page'
      },
      {
        chunks: ['game', 'single', 'main', 'critical'],
        title: 'WAS - Quiz',
        template: 'game',
        bodyClass: 'post-template-default single single-post single-format-standard layout_fixed no-cover dark-theme',
      },
    ],
  },

  CACHE: {
    ENTRIES: {
      'cache/amp': './src/amp.js',
      'cache/admin': './src/admin.js',
    },
  },

  DEV: {
    ENTRIES: {
      'dev/trashy': './src/dev/trashy',
    },
  },
  CUSTOM: {
    ENTRIES: {
      'cache/2019-vote': './src/custom/2019/vote',
    },
    PAGES: [
      {
        chunks: ['cache/2019-metro-clock', 'home', 'main', 'critical'],
        title: 'Metro clock WAS',
        slug: 'metro',
        template: 'home',
        headHtmlSnippet: '',
        bodyClass: 'home blog default-theme',
        bodyHtmlSnippet: 'Home page'
      }
    ]
  },
};
