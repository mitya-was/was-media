<?php
/**
 * WAS Media - Docker Production Config
 * Создано автоматически для деплоя
 */

// ** Database ** //
define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'was');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'was');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'change-me');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'mysql');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** Redis Object Cache ** //
define('WP_REDIS_HOST', getenv('REDIS_HOST') ?: 'redis');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_TIMEOUT', 1);
define('WP_REDIS_READ_TIMEOUT', 1);
define('WP_REDIS_DATABASE', 0);

// ** Performance ** //
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
define('WP_POST_REVISIONS', 5);
define('AUTOSAVE_INTERVAL', 300);
define('EMPTY_TRASH_DAYS', 7);

// ** Debug (ВЫКЛЮЧЕНО на production!) ** //
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

// ** WordPress URLs ** //
define('WP_HOME', 'https://was.media');
define('WP_SITEURL', 'https://was.media');

// ** Security ** //
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', false);
define('FORCE_SSL_ADMIN', true);

// ** Auto Updates ** //
define('WP_AUTO_UPDATE_CORE', 'minor');

// ** Security Keys - ВАЖНО: Скопируйте из вашего текущего wp-config.php ** //
// TODO: Заменить на ваши ключи!
define('AUTH_KEY', 'Q>-pLrTm/8a@f,fOfT-={@9e({=Ozh@6nz[A}gr,c6wsk]u==-RLTjW2Z!Q?[Hfc');
define('SECURE_AUTH_KEY', 'Dxr-BMk=}&]Ng?R F}dq%6WOUp)(Q>rn9>_d;Qo;&$Y)p136,&[;HxvV.wFwF@l9');
define('LOGGED_IN_KEY', '(vb>{Q*VMz[ 37c4gwqrL7eb{GvwP11<v+r_uo0fik`1E&y$]Ebgc,f#gDn#-la|');
define('NONCE_KEY', '~>=(<].xO7>{Y~m4?VsgxWH@x8dOjQA@qSj2e(NC3_3ZQ#]mO8@u E5O:1!+3tE9');
define('AUTH_SALT', ')}MTZZa|!2>MIh{[`3qRW8qy)pfWE@<O3uv2u-N3Vh_jimFj&=ix9iE)(V)@^5?<');
define('SECURE_AUTH_SALT', 'X=l#wgiE}$%$2<PT,H`QqlkBpBAAtD-C[*Yv2UEixNOuQ-jePck(vq7DW<bGbK@E');
define('LOGGED_IN_SALT', 'd+b5`nqlwD[o[]^:7h4B2#FMp|3rv<(JW|DpZ+~$<a(Yc`uH=;c{4CBJWU+Ia8UL');
define('NONCE_SALT', 'KO6IznHA,Kg=]*0{Oo9f4%Ev.nfW1M#((mag,r#zlPSdFE^x$T$c/2HRli)yoE%k');

// ** Table Prefix ** //
$table_prefix = 'b_w_';

// ** Absolute Path ** //
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
