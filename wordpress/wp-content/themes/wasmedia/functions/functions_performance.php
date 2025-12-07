<?php
/**
 * Performance Optimizations
 * Оптимизации производительности для WAS Media
 */

// Отключить эмодзи
function was_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'was_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'was_disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'was_disable_emojis');

function was_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    }
    return array();
}

function was_disable_emojis_remove_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
        $urls = array_diff($urls, array($emoji_svg_url));
    }
    return $urls;
}

// Включить нативный lazy loading
add_filter('wp_lazy_loading_enabled', '__return_true');

// Добавить loading="lazy" к изображениям в контенте
function was_add_lazy_loading_to_images($content) {
    if (is_feed() || is_preview() || is_admin()) {
        return $content;
    }
    
    // Добавить loading="lazy" если его еще нет
    $content = preg_replace('/<img((?:(?!loading=)[^>])*)>/i', '<img$1 loading="lazy">', $content);
    
    return $content;
}
add_filter('the_content', 'was_add_lazy_loading_to_images', 20);
add_filter('post_thumbnail_html', 'was_add_lazy_loading_to_images', 20);

// Добавить preconnect и dns-prefetch для внешних ресурсов
function was_add_resource_hints() {
    // Preconnect для критичных ресурсов
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    
    // DNS prefetch для некритичных
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">';
    
    // Если CDN включен
    if (defined('IS_CDN_ENABLED') && IS_CDN_ENABLED) {
        echo '<link rel="preconnect" href="https://cdn.was.media" crossorigin>';
        echo '<link rel="dns-prefetch" href="//cdn.was.media">';
    }
}
add_action('wp_head', 'was_add_resource_hints', 1);

// Ограничить REST API для неавторизованных пользователей
add_filter('rest_authentication_errors', function($result) {
    // Разрешить доступ для авторизованных
    if (is_user_logged_in()) {
        return $result;
    }
    
    // Разрешить некоторые эндпоинты для фронтенда
    $allowed_routes = array(
        '/wp/v2/posts',
        '/wp/v2/pages',
        '/wp/v2/media',
        '/wp/v2/categories',
        '/wp/v2/tags',
    );
    
    $current_route = $_SERVER['REQUEST_URI'] ?? '';
    
    foreach ($allowed_routes as $route) {
        if (strpos($current_route, $route) !== false) {
            return $result;
        }
    }
    
    // Блокировать остальное
    return new WP_Error(
        'rest_disabled',
        'REST API disabled for unauthorized users',
        array('status' => 401)
    );
});

// Отключить XML-RPC если не используется
add_filter('xmlrpc_enabled', '__return_false');

// Удалить версию WordPress из head
remove_action('wp_head', 'wp_generator');

// Удалить RSD link
remove_action('wp_head', 'rsd_link');

// Удалить wlwmanifest link
remove_action('wp_head', 'wlwmanifest_link');

// Удалить shortlink
remove_action('wp_head', 'wp_shortlink_wp_head');

// Оптимизация heartbeat API
add_filter('heartbeat_settings', function($settings) {
    $settings['interval'] = 60; // Увеличить интервал до 60 секунд
    return $settings;
});

// Отключить heartbeat на фронтенде
add_action('init', function() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}, 1);

// Очистка старых ревизий при сохранении поста
add_action('save_post', function($post_id) {
    $revisions = wp_get_post_revisions($post_id);
    
    if (count($revisions) > 5) {
        $revisions_to_delete = array_slice($revisions, 5);
        
        foreach ($revisions_to_delete as $revision) {
            wp_delete_post_revision($revision->ID);
        }
    }
});

// Автоматическая очистка транзиентов раз в день
if (!wp_next_scheduled('was_cleanup_transients')) {
    wp_schedule_event(time(), 'daily', 'was_cleanup_transients');
}

add_action('was_cleanup_transients', function() {
    global $wpdb;
    
    $wpdb->query("
        DELETE FROM {$wpdb->options}
        WHERE option_name LIKE '_transient_timeout_%'
        AND option_value < UNIX_TIMESTAMP()
    ");
    
    $wpdb->query("
        DELETE FROM {$wpdb->options}
        WHERE option_name LIKE '_transient_%'
        AND option_name NOT LIKE '_transient_timeout_%'
        AND option_name NOT IN (
            SELECT CONCAT('_transient_', SUBSTRING(option_name, 20))
            FROM {$wpdb->options}
            WHERE option_name LIKE '_transient_timeout_%'
        )
    ");
});
