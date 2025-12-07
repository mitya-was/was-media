<?php

use Utils\WASTimber;

global $order_count;
global $do_not_duplicate;

function getLocalPage($id) {
    return get_permalink(pll_get_post($id));
}

function true_load_posts() {
    global $do_not_duplicate;

    // NONCE проверка для безопасности
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (empty($nonce) || !wp_verify_nonce($nonce, 'was_ajax_nonce')) {
        wp_die('Security check failed', 403);
    }

    // Санитизация входных данных
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    $current_post_type = isset($_POST['current_post_type']) ? sanitize_text_field($_POST['current_post_type']) : 'post';
    $current_tag = isset($_POST['current_tag']) ? sanitize_text_field($_POST['current_tag']) : '';
    $referer = isset($_SERVER["HTTP_REFERER"]) ? esc_url_raw($_SERVER["HTTP_REFERER"]) : '';

    $r_options = [];
    $do_not_duplicate = array_map('absint', explode(",", $query));
    $items_count = $current_post_type == "microformats" ? 5 : 10;

    $author = false;

    if (!empty($referer) && ($author_anchor = strpos($referer, "author")) !== false) {
        $author_slug = sanitize_text_field(substr($referer, $author_anchor + strlen("author/"), -1));
        $author = get_user_by("slug", $author_slug);
    }

    $args_main['lang'] = pll_get_post_language($do_not_duplicate[0]);
    $args_main['posts_per_page'] = absint($items_count);
    $args_main['post_status'] = 'publish';
    $args_main['post_type'] = in_array($current_post_type, ['post', 'microformats']) ? $current_post_type : 'post';
    $args_main['ignore_sticky_posts'] = true;
    $args_main['post__not_in'] = $do_not_duplicate;

    if ($author) {
        $args_main['author__in'] = [
            absint($author->ID)
        ];
    }

    if (!empty($current_tag) && $current_tag != "false") {
        $args_main['tag'] = $current_tag;

        if ($current_post_type == "microformats") {
            unset($args_main['tag']);

            $args_main['tax_query'] = [
                [
                    'taxonomy' => 'micro_tag',
                    'field'    => 'slug',
                    'terms'    => $current_tag
                ]
            ];

            $r_options = [
                'cover'         => false,
                'author_info'   => false,
                'content_part'  => 'content',
                'class_format' => '',
                '_class_format' => ' format-micro '
            ];
        }
    }

    $index_posts = new WP_Query($args_main);

    if ($index_posts->have_posts()) {
        render_ajax($index_posts, false, $items_count, $r_options);
    }

    if ($index_posts->post_count > 0) {
        echo '<span class="was_index_load_more" data-not="' . implode(',', $do_not_duplicate) . '"></span>';
    }

    die();
}

add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');

/**
 * @param WP_Query $query
 * @param boolean  $is_feature
 * @param int      $count
 * @param array    $render_options
 */
function render_ajax($query, $is_feature, $count = 1, $render_options = []) {
    global $post;
    global $order_count;
    global $do_not_duplicate;

    for ($i = 0; $i < $count; $i++) {
        $query->the_post();

        if ($post) {
            $post_type = get_post_type($post);
            $do_not_duplicate[] = $post->ID;
            $order_count = make_order_counter($order_count);
            $class_format = $order_count;
            $class_format .= ' post ';
            $class_format .= (is_article_options('invert_text_color', $post)) ? ' invert_text_color' : '';
            $variables = [
                'content_part'  => ($post_type == 'post') ? 'content' : $post_type,
                'author_info'   => true,
                'views_info'    => false,
                'post'          => $post,
                'template_part' => 'index',
                'post_order'    => $order_count,
                'cover'         => !is_article_options('no_cover_single', $post) || !is_single($post->ID) ? true : false,
                'thumb'         => ($is_feature) ? 'format-image-cover' : 'thumbnail',
                'tags'          => 'article',
                'heading'       => 'h1',
                'class_format'  => $class_format
            ];

            // ToDo: make custom button for no-cover posts in listings
            $variables['cover'] = ($post->post_type == 'microformats'  && $variables['cover'] == true && is_article_options('no_cover_single', $post)) ? false : true;

            if (isset($render_options['_class_format'])) {
                $variables['class_format'] .= $render_options['_class_format'];
	            $render_options['class_format'] .= $render_options['_class_format'];
            }
            $variables = array_replace($variables, $render_options);

            echo WASTimber::timber_content_parts_resolver($variables);
        }
    }

    wp_reset_postdata();
}

define("WAS_POST_REQUIRED_FIELDS", ["thumbnail", "tax_input"]);

function was_check_required($post_id) {
    global $post;

    $toDraft = false;

    if ($post && get_post_type($post_id) == 'post' && count($_POST) > 0) {
        checkSEOSuffixes($post);

        $hasThumb = has_post_thumbnail($post_id);

        if (!$hasThumb && get_post_status($post_id) === "publish") {
            $toDraft = true;

            set_transient("has_post_thumbnail", "You must select Featured Image. Your Post is saved as DRAFT.");
        } else {
            delete_transient("has_post_thumbnail");
        }

//         if (count($_POST["tax_input"]["post_tag"]) < 5) {
//             $toDraft = true;

//             set_transient("has_post_tax_input", "You must set at least 5 tags. Post is saved as DRAFT.");
//         } else {
//             delete_transient("has_post_tax_input");
//         }

        if ($toDraft && get_post_status($post_id) === "publish") {
            remove_action('save_post', 'was_check_required');

            wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);

            add_action('save_post', 'was_check_required');
        }
    }
}

/**
 * @param WP_Post $post
 */
function checkSEOSuffixes($post) {
    $SEOSeparator = getSEOSeparator();

    if ($post) {
        $WASSEOTitleSuffix = " " . $SEOSeparator . " WAS";
        $WASSEOTitles = ["yoast_wpseo_title", "yoast_wpseo_opengraph-title", "yoast_wpseo_twitter-title"];

        foreach ($WASSEOTitles as $WASSEOTitle) {
            $title = trim($_POST[$WASSEOTitle]);

            if ($title) {
                $_POST[$WASSEOTitle] = trim(preg_replace(
                        "/(?'text'.*?)(\.\s" . $SEOSeparator . "\sWAS)$|(?&text)(\." . $SEOSeparator .
                        "WAS)$|(?&text)(\s\\" . $SEOSeparator . "\sWAS)$|(?&text)(\\" . $SEOSeparator .
                        "WAS)$|(?&text)(\.)$|(?&text)($)/is",
                        "$1",
                        $title
                    )) . $WASSEOTitleSuffix;
            }
        }
    }
}

add_action('save_post', 'was_check_required');

function was_required_error() {
    foreach (WAS_POST_REQUIRED_FIELDS as $required) {
        $error = get_transient("has_post_" . $required);

        if ($error) {
            echo "<div id='message' class='error'><p><strong>" . $error . "</strong></p></div>";

            delete_transient("has_post_" . $required);
        }
    }
}

add_action('admin_notices', 'was_required_error');

function SEOSeparator() {
    ?>
    <div id="yoast_seo_separator" class="hidden">
        <?= getSEOSeparator() ?>
    </div>
    <?php
}

add_action('post_submitbox_misc_actions', 'SEOSeparator');

function getSEOSeparator() {
    $separator = '-';
    $wpseo_titles = get_option('wpseo_titles');
    $sep_options = (class_exists("WPSEO_Option_Titles")) ?
        WPSEO_Option_Titles::get_instance()->get_separator_options() :
        false;

    if ($sep_options && isset($wpseo_titles['separator']) && isset($sep_options[$wpseo_titles['separator']])) {
        $separator = html_entity_decode($sep_options[$wpseo_titles['separator']]);
    }

    return $separator;
}

function wpseo_show_article_author_only_on_posts($facebook) {
    global $post;

    if ($post && is_single()) {
        $options = get_field("article_options", $post->ID);

        if ($options && is_array($options) && in_array("show_author_meta", $options)) {
            return $facebook;
        }
    }

    return false;
}

add_filter('wpseo_opengraph_author_facebook', 'wpseo_show_article_author_only_on_posts', 10, 1);

// Admin panel filtering by post format
function wpse26032_admin_posts_filter(&$query) {
    if (
        is_admin()
        AND 'edit.php' === $GLOBALS['pagenow']
        AND isset($_GET['p_format'])
        AND '-1' != $_GET['p_format']
    ) {
        $query->query_vars['tax_query'] = [
            [
                'taxonomy' => 'post_format',
                'field'    => 'ID',
                'terms'    => [
                    $_GET['p_format']
                ]
            ]
        ];
    }
}

add_filter('parse_query', 'wpse26032_admin_posts_filter');

function wpse26032_restrict_manage_posts_format() {
    wp_dropdown_categories([
        'taxonomy'         => 'post_format',
        'hide_empty'       => true,
        'name'             => 'p_format',
        'show_option_none' => 'Select Post Format'
    ]);
}

add_action('restrict_manage_posts', 'wpse26032_restrict_manage_posts_format');

function enable_more_buttons($buttons) {
    $buttons[] = 'styleselect';
    $buttons[] = 'charmap';
    $buttons[] = 'hr';
    $buttons[] = 'visualaid';

    return $buttons;
}

add_filter("mce_buttons_3", "enable_more_buttons");

/* Add subscribers into author select */
function add_subscribers_to_dropdown($query_args) {
    $query_args['who'] = '';

    return $query_args;
}

add_filter('wp_dropdown_users_args', 'add_subscribers_to_dropdown', 10, 2);

/**
 * Insert Post title depend of context and heading tags
 *
 * @param $context
 * @param $heading
 * @param $post
 */
function insert_content_title($context, $heading, $post) {
    $ttl = get_the_title($post);
    $txt = (in_category('games', $post) && !is_game_options('custom', $post)) ? '<span class="siteHeader-header">Тест</span> ' : '';

    if ($context == 'single') {
        echo '<h1 class="' . $heading . ' h-entry">' . $txt . $ttl . '</h1>'; // вывод
    } else {
        echo '<h2 class="' . $heading . ' h-entry">' . $txt . $ttl . '</h2>'; // вывод
    }
}

/**
 * @param WP_Post $post
 *
 * @return array
 */
function was_get_smart_related($post) {
    $RELATED_POST_COUNT = 3;

    $cache_key = 'was_related_' . $post->ID . '_' . (function_exists('pll_get_post_language') ? \pll_get_post_language($post->ID) : '');
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $manual_related_posts = get_field("related_materials", $post->ID) ?: [];

    if (($_related_post_count = count($manual_related_posts)) < $RELATED_POST_COUNT) {
        $exclude = '';

        if ($_related_post_count > 0) {
            $exclude = [];

            /** @var WP_Post $m_post */
            foreach ($manual_related_posts as $m_post) {
                $exclude[] = $m_post->ID;
            }
        }

        $possible_relation = [];
        $terms = wp_get_post_terms($post->ID, "micro_tag") ?: [];

        /** @var WP_Term $term */
        foreach ($terms as $term) {
            $possible_relation = array_merge(
                $possible_relation,
                get_posts(
                    [
                        'lang'         => (function_exists('pll_get_post_language') ? \pll_get_post_language($post->ID) : ''),
                        'status'       => 'published',
                        'post_type'    => 'post',
                        'post__not_in' => $exclude,
                        'tax_query'    => [
                            [
                                'taxonomy' => 'post_tag',
                                'field'    => 'name',
                                'terms'    => $term->name
                            ]
                        ]
                    ]
                )
            );
        }

        if (count($possible_relation) > 0) {
            $exclude = [];
            $related_left = $RELATED_POST_COUNT - $_related_post_count;

            for ($i = 0; $i < $related_left; $i++) {

                if (count($possible_relation) === 0) {
                    break;
                }

                $random_item_index = rand(0, (count($possible_relation) - 1));

                if (!in_array(($possible_relation[$random_item_index])->ID, $exclude)) {
                    $manual_related_posts[] = $possible_relation[$random_item_index];

                    $exclude[] = ($possible_relation[$random_item_index])->ID;

                    unset($possible_relation[$random_item_index]);
                    $possible_relation = array_values($possible_relation);
                }
            }
        }
    }

    set_transient($cache_key, $manual_related_posts, HOUR_IN_SECONDS);
    return $manual_related_posts;
}

/**
 * @param string $url
 *
 * @return string
 */
function process_url_for_embeding(string $url): string {

    switch (true) {

        case preg_match('/(?:youtube.com\/)((((?:embed)|(?:watch))((?:\?v\=)|(?:\/)))([\w\-_]+)(?:\?start=(\w+))*)/i', $url, $matches) :
            $params = isset($matches[6]) ? "?start={$matches[6]}&rel=0" : '?rel=0';
            $url = "https://www.youtube.com/embed/{$matches[5]}{$params}";
            break;

        case preg_match('/(?:youtu.be\/)([\w-_]+)(?:\?t=(\w+))*/i', $url, $matches) :
            $params = isset($matches[2]) ? "?start={$matches[2]}&rel=0" : '?rel=0';
            $url = "https://www.youtube.com/embed/{$matches[1]}{$params}";
            break;

        case preg_match('/(?:vimeo.com\/)(\w+)/i', $url, $matches) :
            $params = "?background=1";
            $url = "https://player.vimeo.com/video/{$matches[1]}{$params}";
            break;

        case preg_match("/(?:http:\/\/)?(?:www\.)?twitter\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-]*)/", $url) :
            $url = "https://twitframe.com/show?url=" . urlencode($url);
            break;

        default:
            break;
    }

return $url;
}

function get_random_post() {
$current_language = function_exists('pll_current_language') ? \pll_current_language() : '';
$total = (int)wp_count_posts('post')->publish;
$offset = $total > 50 ? rand(0, 50) : rand(0, max(0, $total - 1));
$random_posts = get_posts([
    'lang'              => $current_language,
    'numberposts'       => 1,
    'posts_per_page'    => 1,
    'orderby'           => 'date',
    'order'             => 'DESC',
    'offset'            => $offset,
    'post_type'         => 'post',
    'post_status'       => 'publish',
    'category__not_in'  => ['games'],
]);

return $random_posts ? $random_posts[0] : null;
}

add_action('save_post', function($post_id){
if (wp_is_post_revision($post_id)) return;
$lang = function_exists('pll_get_post_language') ? \pll_get_post_language($post_id) : '';
delete_transient('was_related_' . $post_id . '_' . $lang);
});

/**
 * Output views counter block for a post.
 * Safe fallback: shows stored meta counter if present, otherwise 0.
 * Used from Twig via function('was_count_block', ...).
 *
 * @param string $mode Unused legacy flag (e.g. 'off').
 * @param mixed  $post Timber\Post|WP_Post|int|null
 * @return string HTML span with formatted views count
 */
function was_count_block($mode = 'off', $post = null) {
    $post_id = 0;

    if ($post instanceof \Timber\Post) {
        $post_id = (int) $post->ID;
    } elseif ($post instanceof \WP_Post) {
        $post_id = (int) $post->ID;
    } elseif (is_numeric($post)) {
        $post_id = (int) $post;
    } else {
        $current = get_post();
        $post_id = $current ? (int) $current->ID : 0;
    }

    if ($post_id <= 0) {
        return '';
    }

    $views = 0;
    foreach (['post_views_count', 'views', 'was_views'] as $meta_key) {
        $val = get_post_meta($post_id, $meta_key, true);
        if ($val !== '' && $val !== false) {
            $views = (int) $val;
            break;
        }
    }

    return '<span class="was-views-count">' . number_format_i18n($views) . '</span>';
}
