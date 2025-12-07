<?php
/** Enable W3 Total Cache */
define('WP_POST_REVISIONS', 5); // Уменьшено с 20 до 5
define('AUTOSAVE_INTERVAL', 300); // Увеличено с 120 до 300 секунд
define('WP_DEBUG', false);
define('IS_DEV', false); //Will enable 'maintenance' mode on dev server
define('IS_CDN_ENABLED', 'mirror'); // TRUE - CDN via Imgix; 'mirror' - rewrite to CDN domain; FALSE - off
define('CDN_DOMAIN', 'cdn.was.media'); // BunnyCDN pull zone CNAME
define('COLLECT_GAME_STATS', false); //TRUE - will collect stats not only when post is published; FALSE - only if post is published
define('TWIG_CACHE_TIME', 3600); // Увеличено с 600 до 3600 секунд (1 час)
define('WPCACHEHOME', '/opt/site/public_html/wordpress/wp-content/plugins/wp-super-cache/');
define('WP_MEMORY_LIMIT', '256M'); // Уменьшено с 1024M до 256M
define('WP_MAX_MEMORY_LIMIT', '512M'); // Для админки
define('RT_WP_NGINX_HELPER_CACHE_PATH', $_SERVER['RT_WP_NGINX_HELPER_CACHE_PATH'] ?? '/var/run/nginx-cache');
define('WP_CACHE', true);
// Redis connection (overridable via env)
define('WP_REDIS_HOST', getenv('WP_REDIS_HOST') ?: (getenv('REDIS_HOST') ?: 'was_redis'));
define('WP_REDIS_PORT', getenv('WP_REDIS_PORT') ?: 6379);
define('WP_REDIS_DATABASE', 0);
define('WP_REDIS_TIMEOUT', 1.0);
define('WP_REDIS_READ_TIMEOUT', 1.0);
define('WP_REDIS_SCHEME', 'tcp');

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'was');
/** MySQL database username */
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'was');
/** MySQL database password */
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'change-me');
/** MySQL hostname */
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'host.docker.internal:3309');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service} You can change these at any
 * point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'Q>-pLrTm/8a@f,fOfT-={@9e({=Ozh@6nz[A}gr,c6wsk]u==-RLTjW2Z!Q?[Hfc');
define('SECURE_AUTH_KEY', 'Dxr-BMk=}&]Ng?R F}dq%6WOUp)(Q>rn9>_d;Qo;&$Y)p136,&[;HxvV.wFwF@l9');
define('LOGGED_IN_KEY', '(vb>{Q*VMz[ 37c4gwqrL7eb{GvwP11<v+r_uo0fik`1E&y$]Ebgc,f#gDn#-la|');
define('NONCE_KEY', '~>=(<].xO7>{Y~m4?VsgxWH@x8dOjQA@qSj2e(NC3_3ZQ#]mO8@u E5O:1!+3tE9');
define('AUTH_SALT', ')}MTZZa|!2>MIh{[`3qRW8qy)pfWE@<O3uv2u-N3Vh_jimFj&=ix9iE)(V)@^5?<');
define('SECURE_AUTH_SALT', 'X=l#wgiE}$%$2<PT,H`QqlkBpBAAtD-C[*Yv2UEixNOuQ-jePck(vq7DW<bGbK@E');
define('LOGGED_IN_SALT', 'd+b5`nqlwD[o[]^:7h4B2#FMp|3rv<(JW|DpZ+~$<a(Yc`uH=;c{4CBJWU+Ia8UL');
define('NONCE_SALT', 'KO6IznHA,Kg=]*0{Oo9f4%Ev.nfW1M#((mag,r#zlPSdFE^x$T$c/2HRli)yoE%k');
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'b_w_';
/* Turn off auto update */
define('WP_AUTO_UPDATE_CORE', false);
define("OTGS_DISABLE_AUTO_UPDATES", true);
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
