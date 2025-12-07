<?php

use Utils\Response;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function twentyseventeen_setup() {
    /*
     * Make theme available for translation.
     * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentyseventeen
     * If you're building a theme based on Twenty Seventeen, use a find and replace
     * to change 'twentyseventeen' to the name of your theme in all the template files.
     */
    load_theme_textdomain('was');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support('title-tag');

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support('html5', [
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);

    /*
     * Enable support for Post Formats.
     *
     * See: https://codex.wordpress.org/Post_Formats
     */
    /* Init post formats and rename */
    add_theme_support('post-formats', ['standard', 'image', 'video', 'status']);

    function rename_post_formats($translation, $text, $context) {
        $names = [
            'Standard' => 'Main',
            'Image'    => 'Feature',
            'Video'    => 'Video',
            'Status'   => 'Cover'
        ];

        if ($context == 'Post format') {
            $translation = str_replace(array_keys($names), array_values($names), $text);
        }

        return $translation;
    }

    add_filter('gettext_with_context', 'rename_post_formats', 10, 4);
}

add_action('after_setup_theme', 'twentyseventeen_setup');

/**
 * Register custom fonts.
 */
function twentyseventeen_fonts_url() {
    $fonts_url = '';

    /**
     * Translators: If there are characters in your language that are not
     * supported by Libre Franklin, translate this to 'off'. Do not translate
     * into your own language.
     */
    $libre_franklin = _x('on', 'Roboto font: on or off', 'twentyseventeen');

    if ('off' !== $libre_franklin) {
        $font_families = [];
        $font_families[] = 'Roboto:400,400i,700';
        $query_args = [
            'family' => urlencode(implode('|', $font_families)),
            'subset' => urlencode('cyrillic,latin'),
        ];
        $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }

    return esc_url_raw($fonts_url);
}

/**
 * Remove dns-prefetch
 * https://fonts.googleapis.com
 * https://s.w.org
 *
 * @param $hints
 * @param $relation_type
 *
 * @return array
 */
function remove_dns_prefetch($hints, $relation_type) {

    if ('dns-prefetch' === $relation_type) {
        return array_diff(wp_dependencies_unique_hosts(), $hints);
    }

    return $hints;
}

add_filter('wp_resource_hints', 'remove_dns_prefetch', 10, 2);

/**
 * Enqueue scripts and styles.
 */
function twentyseventeen_scripts() {
    $style_ver = null;
    $script_ver = null;

    // deregister scripts
    if (!is_admin()) {
        wp_deregister_script('jquery');
    }

    wp_deregister_script('wp-embed');

    // Register additional js:
    wp_register_script('polyfills', 'https://cdnjs.cloudflare.com/ajax/libs/dom4/2.0.0/dom4.js', [], null, true);
    wp_register_script('swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/js/swiper.min.js',
        [], null, true);
    wp_register_script('magicscroll', 'https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.2/ScrollMagic.min.js',
        [], null,
        true);
    wp_register_script('tingle', 'https://cdnjs.cloudflare.com/ajax/libs/tingle/0.13.2/tingle.min.js',
        [], null, true);
    wp_register_script('tether', 'https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.4/js/tether.min.js',
        [], null, true);
    wp_register_script('tether-drop', 'https://cdnjs.cloudflare.com/ajax/libs/tether-drop/1.4.2/js/drop.min.js',
        [], null, true);

    wp_register_script('main', get_hashed_asset_file("js", "main"), [], $script_ver, true);
    wp_register_script('single', get_hashed_asset_file("js", "single"), [], $script_ver, true);
    wp_register_script('game', get_hashed_asset_file("js", "game"), [], $script_ver, true);

    // Register main style
    wp_register_style('font', twentyseventeen_fonts_url(), [], null, "none");

    wp_register_style('tether-drop', 'https://cdnjs.cloudflare.com/ajax/libs/tether-drop/1.4.2/css/drop-theme-arrows-bounce-dark.min.css',
        [], null, "none");
    wp_register_style('swiper-css', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/css/swiper.min.css',
        [], null, "none");
    wp_register_style('style', get_hashed_asset_file("css", "main"),
        [], $style_ver, "none");
    wp_register_style('single', get_hashed_asset_file("css", "single"),
        [], $style_ver, "none");
    wp_register_style('archive', get_hashed_asset_file("css", "archive"),
        [], $style_ver, "none");
    wp_register_style('game', get_hashed_asset_file("css", "game"),
        [], $style_ver, "none");

    $current_queried_object = get_queried_object();
    $scripts = ['polyfills', 'swiper', 'magicscroll', 'tingle', 'tether', 'tether-drop', 'main'];
    $styles = ['font', 'swiper-css', 'tether-drop', 'style'];

    if (is_singular(["post", "page"]) || in_array(get_post_type(), ["podcasts"]) || is_home()) {
        $poststyle = "single";
        $postscript = "single";

        if (is_home()) {
            array_pop($scripts);

            $poststyle = "home";
            $postscript = "main";
        }

        if (is_page('newsletter-subscribe')) {
            $poststyle = "newsletter";
            $postscript = "newsletter";

        }

        if (!is_home() && in_category('games')) {
            array_push($scripts, "single");
            array_push($styles, "single");

            $poststyle = "game";
            $postscript = "game";
        }

    } else {
        array_pop($styles);
        array_pop($scripts);

        if (is_archive() || is_search()) {
            array_push($styles, "archive");
        }

        $poststyle = "main";
        $postscript = "main";
    }

    if (
        (($current_queried_object instanceof WP_Post_Type &&
                $current_queried_object->query_var === "microformats") ||
            ($current_queried_object instanceof WP_Post &&
                $current_queried_object->post_type === "microformats")) ||
        ($current_queried_object instanceof WP_Term && $current_queried_object->taxonomy === "micro_tag")
    ) {
        $poststyle = "microformats";
        $postscript = "microformats";

        array_push($scripts, "main");
        array_push($styles, "style");
    }

    wp_enqueue_script(
        'script',
        get_hashed_asset_file("js", $postscript),
        $scripts,
        $script_ver,
        true
    );

    // Provide AJAX nonce and endpoints to front-end scripts
    wp_localize_script('script', 'wasAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('was_ajax_nonce'),
    ]);

    wp_enqueue_style(
        'main',
        get_hashed_asset_file("css", $poststyle),
        $styles,
        $style_ver,
        "none"
    );

    if (is_single(41373)) {
        wp_enqueue_script("vote-script", get_template_directory_uri() . '/assets/js/cache/2019-vote.js', [], null, true);
        wp_enqueue_style("vote-style", get_template_directory_uri() . '/assets/css/cache/2019-vote.css', [], null);
    }

    if (is_admin()) {
        wp_enqueue_style(
            'trashy',
            get_hashed_asset_file('css', 'dev/trashy'),
            [],
            null,
            "all");
    }
}

add_action('wp_enqueue_scripts', 'twentyseventeen_scripts');

function add_noscript_style_filter($tag, $handle, $href, $media) {

    if ('none' === $media) {
        $noscript = '<noscript>';
        $noscript .= preg_replace("/='none'/", "='all'", $tag);
        $noscript .= '</noscript>';

        $tag = $tag . $noscript;
    }

    return $tag;
}

add_filter('style_loader_tag', 'add_noscript_style_filter', 10, 4);

/* Styles admin dashboard */
function my_styles_scripts_admin() {
    wp_enqueue_style("style-admin", get_hashed_asset_file('css', 'cache/admin'), [], null);
    wp_enqueue_script("script-admin", get_hashed_asset_file('js', 'cache/admin'), [], null);
}

add_action('admin_head', 'my_styles_scripts_admin');

function my_style_loader_tag_function($tag) {

    if (is_admin()) {
        return $tag;
    }

    return preg_replace("/='none'/", "='none' onload=\"if(media!='all')media='all'\"", $tag);
}

add_filter('style_loader_tag', 'my_style_loader_tag_function');

function add_defer_attribute($tag, $handle) {
    $defer_load = [''];

    if (is_admin() || in_array($handle, $defer_load)) {
        return $tag;
    }

    return str_replace(' src', ' defer src', $tag);
}

add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);

// Get rid of WP api's
function remove_api() {
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    // remove emoji icons
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'feed_links_extra', 3);
    //remove_action( 'wp_head', 'feed_links', 2 );
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10);
    remove_action('wp_head', 'start_post_rel_link', 10);
    remove_action('wp_head', 'adjacent_posts_rel_link', 10);
    remove_action('wp_head', 'profile_link');
}

add_action('after_setup_theme', 'remove_api');

// close and add redirect from date archive pages
function redirect_to_home() {

    if (is_date()) {
        wp_redirect(home_url());
        exit;
    }
}

add_action('parse_query', 'redirect_to_home');

// Show Tags
function themeprefix_show_tags($args) {

    if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'get-tagcloud') {
        unset($args['number']);
        $args['hide_empty'] = 0;
    }

    return $args;
}

add_filter('get_terms_args', 'themeprefix_show_tags');

function make_order_counter($order_count = 0) {
    return ++$order_count;
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function twentyseventeen_body_classes($classes) {
    global $post;
    global $wp_query;

    $current_queried_object = get_queried_object();
    $is_post_tag = $current_queried_object instanceof WP_Term && $current_queried_object->taxonomy === "post_tag";
    $is_micro_tag = $current_queried_object instanceof WP_Term && $current_queried_object->taxonomy === "micro_tag";

    if (has_tag('top')) {
        $classes[] = 'tag-top';
    }

    if (!is_author() &&
        (is_single() ||
            ("microformats" == get_post_type() && !$is_post_tag && !is_search()) ||
            is_page() ||
            (is_search() && $wp_query->found_posts <= 0))
    ) {
        $classes[] = 'layout_fixed';
    }

    if (is_front_page() && 'posts' !== get_option('show_on_front')) {
		$classes[] = 'front-page';
	}

    if (is_singular() && (is_article_options("no_cover_single", $post))) {
        $classes[] = 'no-cover';
    }

    if (!is_author() &&
        ((("microformats" == get_post_type() && !$is_post_tag && !is_search()) || $is_micro_tag) ||
            (is_singular() && is_article_options("template_center", $post) || has_tag('game')))
    ) {
        $classes[] = 'template-center';
    }

    if (is_singular() && is_article_options("dark_theme", $post)) {
        $classes[] = 'dark-theme';
    } else {
        $classes[] = 'default-theme';
    }

    return $classes;
}

add_filter('body_class', 'twentyseventeen_body_classes');

function was_custom_classes_to_html($output, $doctype) {

    if ($doctype !== 'html') {
        return $output;
    }

    if (is_user_logged_in() &&
        in_array('bottom_admin_bar', (array) get_field('additional_options', _wp_get_current_user()))) {
        $output .= ' data-abar="custom_bottom_a_b" ';
    }

    return $output;
}

add_filter('language_attributes', 'was_custom_classes_to_html', 10, 2);

/**
 * Count our number of active panels.
 *
 * Primarily used to see if we have any panels active, duh.
 */
function twentyseventeen_panel_count() {
    $panel_count = 0;

    /**
     * Filter number of front page sections in Twenty Seventeen.
     *
     * @param $num_sections integer
     *
     * @since Twenty Seventeen 1.0
     *
     */
    $num_sections = apply_filters('twentyseventeen_front_page_sections', 4);
    // Create a setting and control for each of the sections available in the theme.
    for ($i = 1; $i < (1 + $num_sections); $i++) {

        if (get_theme_mod('panel_' . $i)) {
            $panel_count++;
        }
    }

    return $panel_count;
}

function is_article_options($value, $post) {
    $article_options = get_field('article_options', $post->ID);

    return ($article_options && in_array($value, $article_options));
}

function is_game_options($value, $post) {
    $options = get_field('admin_post_layout', $post->ID);
    foreach ($options as $option){
        if (isset($option['game_options']) && count($option['game_options']) > 0){
            return (in_array($value, $option['game_options']));
        }
    }
    return false;
}

/**
 * Checks to see if we're on the homepage or not.
 */
function twentyseventeen_is_frontpage() {
    return (is_front_page() && !is_home());
}

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 * @since Twenty Seventeen 1.0
 *
 */
function twentyseventeen_front_page_template($template) {
    return is_home() ? '' : $template;
}

add_filter('frontpage_template', 'twentyseventeen_front_page_template');

function was_get_entry($str, $template_options, $post) {

    if ($str == "author" && $post->post_type === 'post') {

        if (has_tag('special', $post)) {
            if ($template_options == 'single') {
                $tag = get_term_by('slug', 'special', 'post_tag');
                echo '<a class="lnkTxt" href="' . get_tag_link($tag) . '">' . pll__("Cпецпроект") . '</a>';
            } else {
                echo '<p>' . pll__("Cпецпроект") . '</p>';
            }

        } else {

            if ($template_options == 'single') {
                echo get_field('post_author', $post->ID) ? get_field('post_author', $post->ID) :
                    '<a class="lnkTxt" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' .
                    get_the_author_meta('description') . '</a>';
            }
        }

    } elseif ($post->post_type === 'microformats' && is_home()) {
        $link = home_url() . '/microformats';
        echo '<a class="lnkTxt" href="' . $link . '">' . pll__("Короткие истории") . '</a>';

    } else {
        echo "<p class=\"article-lead\">" . get_the_excerpt($post) . "</p>";
    }
}

function was_get_sound_ico($post) {
    $result = "";

    if ($post && have_rows('admin_post_layout', $post->ID)) {

        while (have_rows('admin_post_layout', $post->ID)) {
            the_row();

            $layout = get_row_layout();

            if ($layout && $layout == 'add_elastic' && get_sub_field('elastic_type') == 'podcast') {
                $result = '<span style="display: block">';
                $result .= twentyseventeen_get_svg(['icon' => 'audio', 'class' => "icon-lg"]);
                $result .= '</span>';

                break;
            }
        }

        reset_rows();
    }

    return $result;
}

function cat_title() {

    if (is_category() || is_tag()) {
        single_cat_title("#");

    } elseif (is_author()) {
        echo get_the_author_meta('description');
    }
}

/**
 * Add excerpt filed for pages
 */
function my_add_excerpts_to_pages() {
    add_post_type_support('page', 'excerpt');
}

add_action('init', 'my_add_excerpts_to_pages');

/**
 * Add post-formats to post_type 'page'
 */
function twentyseventeen_add_post_formats_to_page() {
    add_post_type_support('page', 'post-formats');
    register_taxonomy_for_object_type('post_format', 'page');
}

add_action('init', 'twentyseventeen_add_post_formats_to_page', 11);

/**
 * Check if url address has param string
 *
 * @param $str
 *
 * @return bool
 */
function is_string_url($str) {
    return (strpos($_SERVER['REQUEST_URI'], $str) !== false);
}

/**
 * @param WP_Query $query - customize $query for taxonomies
 */
function taxonomy_posts_per_page($query) {

    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (is_search() || is_tag() || is_author()) {
        $exclude = [
            'page',
            'attachment',
            'revision',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'polylang_mo',
            'acf-field-group',
            'acf-field',
            'vecb_editor_buttons'
        ];

        $query->set('post_type', array_diff(get_post_types(), $exclude));
        $query->set('posts_per_page', 12);
        $query->set('no_found_rows', true);

        return;
    }

    if (!current_user_can("edit_posts")) {
        $query->set('post_status', ['publish', 'future']);
    }
}

add_action('pre_get_posts', 'taxonomy_posts_per_page', 100);

// Enforce rel noopener/noreferrer on target=_blank links
add_filter('wp_targeted_link_rel', function($rel_values) {
    $rels = array_filter(array_map('trim', explode(' ', (string)$rel_values)));
    $rels = array_unique(array_merge($rels, ['noopener','noreferrer']));
    return trim(implode(' ', $rels));
});

// Fallback ALT for images: use attachment title if alt is empty
add_filter('wp_get_attachment_image_attributes', function($attr, $attachment) {
    if (empty($attr['alt'])) {
        $alt = trim(get_post_meta($attachment->ID, '_wp_attachment_image_alt', true));
        if ($alt === '') {
            $alt = get_the_title($attachment->ID);
        }
        $attr['alt'] = $alt ?: '';
    }
    return $attr;
}, 10, 2);

// Meta description fallback if theme doesn't output it
add_action('wp_head', function(){
    if (is_admin()) return;
    if (is_singular()) {
        $desc = '';
        if (function_exists('get_post_meta')) {
            $yoast = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
            if (!empty($yoast)) { $desc = $yoast; }
        }
        if ($desc === '') {
            $excerpt = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags(get_post_field('post_content', get_the_ID()), true);
            $desc = wp_trim_words($excerpt, 40, '');
        }
        $desc = esc_attr($desc);
        if ($desc !== '') {
            echo "<meta name=\"description\" content=\"{$desc}\">\n";
        }
    }
}, 5);

// Add x-default hreflang (Polylang)
add_action('wp_head', function(){
    if (function_exists('pll_the_languages')) {
        $home = home_url('/');
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($home) . '">\n';
    }
}, 5);

function future_permalink($permalink, $post, $leavename, $sample = false) {
    static $recursing = false;

    if (empty($post->ID)) {
        return $permalink;
    }

    if (!$recursing && isset($post->post_status) && ('future' === $post->post_status)) {
        $post->post_status = 'publish';
        $recursing = true;

        return get_permalink($post, $leavename);
    }

    $recursing = false;

    return $permalink;
}

add_filter('post_link', 'future_permalink', 10, 3);
add_filter('post_type_link', 'future_permalink', 10, 4);

/**
 * Prints HTML tag navs with meta information.
 */

function get_wpb_tags() {
    $tags = get_tags();
    $string = '';
    $i = 0;

    foreach ($tags as $tag) {

        if ($i < 5) {
            $string .= '<li class="nav-item"><a class="nav-link taglink" href="' . get_tag_link($tag->term_id) . '">#' .
                $tag->name . '</a></li>';
        }

        $i++;
    }

    echo $string;
}

function cloudbox($tags_raw, $count, $offset, $static = false) {

    if ($tags_raw && count($tags_raw) > 0) {

        if ($static === false) {
            $special_link = home_url() . "/tag/special";
            $special_name = pll__("Cпецпроект");

            $micro_link = home_url() . "/microformats";
            $micro_name = pll__("Коротко");

	        $random_post = get_random_post();
            $random_link = get_post_permalink($random_post->ID);
            // Ensure proper locale label for Ukrainian; fallback to Polylang string for others
            $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';
            if ($current_lang === 'uk') {
                $random_name = 'Випадкова стаття';
            } else {
                $random_name = pll__('Случайная статья');
            }

            echo "
				 <li class='nav-item hidden-md-up'><a href='$random_link' class='nav-link taglink color-brand'>$random_name</a>$random_post->id</li>
				 <li class='nav-item'><a href='$micro_link' class='nav-link taglink color-brand'>$micro_name</a></li>";
         	//        <li class='nav-item'><a href='$special_link' class='nav-link taglink color-brand'>$special_name</a></li>";
        }

        $tags = array_slice($tags_raw, $offset, $count);
        $count = 0;

        foreach ($tags as $tag) {
            $remove_style_tag = preg_replace('/style="font-size:.+pt;"/', '', $tag);
            $remove_title_tag = preg_replace('/title=".+"/', '', $remove_style_tag);

            $tag_class = ($static === false && $count > 2) ? 'nav-item hidden-xl-down' : 'nav-item';

            echo '<li class="' . $tag_class . '">' . preg_replace('/class="/', 'class="nav-link taglink ', $remove_title_tag) .
                '</li>';

            $count++;
        }
    }
}

function get_tags_cloud($count) {
    return (function_exists('wp_tag_cloud')) ?
        wp_tag_cloud([
            'smallest' => 25,
            'largest'  => 25,
            'unit'     => 'pt',
            'orderby'  => 'count',
            'order'    => 'DESC',
            'format'   => 'array',
            'exclude'  => '28,39,43,45,53,79,117,354,1055,2051,2053,2085,2277,2269,2283,2297',
            'number'   => $count
        ]) :
        false;
}

function get_micro_tags_cloud($count, $exclude = null) {
    return (function_exists('wp_tag_cloud')) ?
        wp_tag_cloud([
            'smallest' => 25,
            'largest'  => 25,
            'unit'     => 'pt',
            'orderby'  => 'count',
            'order'    => 'DESC',
            'format'   => 'array',
            'taxonomy' => 'micro_tag',
            'exclude'  => $exclude,
            'number'   => $count
        ]) :
        false;
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = "";
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;

        return $str;
    }
}

/**
 * Prints class for tag links
 *
 * @param $links
 *
 * @return mixed
 */
function add_tag_class($links) {
    return str_replace('<a href="', '<a class="nav-link taglink" href="', $links);
}

add_filter("term_links-post_tag", 'add_tag_class');

if (!function_exists('twentyseventeen_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function twentyseventeen_posted_on() {
        // Get the author name; wrap it in a link.
        $byline = sprintf(
        /* translators: %s: post author */
            __('by %s', 'twentyseventeen'),
            '<span class="author vcard"><a class="url fn n" href="' .
            esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . get_the_author() . '</a></span>'
        );
        // Finally, let's write all of this to the page.
        echo '<span class="posted-on">' . twentyseventeen_time_link() . '</span><span class="byline"> ' . $byline .
            '</span>';
    }
endif;

if (!function_exists('twentyseventeen_time_link')) :
    /**
     * Gets a nicely formatted string for the published date.
     */
    function twentyseventeen_time_link() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf($time_string,
            get_the_date(DATE_W3C),
            get_the_date(),
            get_the_modified_date(DATE_W3C),
            get_the_modified_date()
        );

        // Wrap the time string in a link, and preface it with 'Posted on'.
        return sprintf(
        /* translators: %s: post date */
            __('<span class="screen-reader-text">Posted on</span> %s', 'twentyseventeen'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );
    }
endif;

if (!function_exists('twentyseventeen_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function twentyseventeen_entry_footer() {
        /* translators: used between list items, there is a space after the comma */
        $separate_meta = __('&nbsp', 'twentyseventeen');
        // Get Tags for posts.
        $tags_list = get_the_tag_list('', $separate_meta);
        // We don't want to output .entry-footer if it will be empty, so make sure its not.
        if (((twentyseventeen_categorized_blog()) || $tags_list) || get_edit_post_link()) {

            if ('post' === get_post_type()) {

                if ((twentyseventeen_categorized_blog()) || $tags_list) {
                    echo '<ul class="navbar-nav">';

                    if ($tags_list) {
                        echo $tags_list;
                    }
                }
            }

            echo '</ul>';
        }
    }
endif;

if (!function_exists('twentyseventeen_edit_link')) :
    /**
     * Returns an accessibility-friendly link to edit a post or page.
     *
     * This also gives us a little context about what exactly we're editing
     * (post or page?) so that users understand a bit more where they are in terms
     * of the template hierarchy and their content. Helpful when/if the single-page
     * layout with multiple posts/pages shown gets confusing.
     */
    function twentyseventeen_edit_link() {

        $link = edit_post_link(
            sprintf(
            /* translators: %s: Name of current post */
                __('Edit<span class="lnkTxt"> "%s"</span>', 'twentyseventeen'),
                get_the_title()
            ),
            '<span class="edit-link">',
            '</span>'
        );

        return $link;
    }
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function twentyseventeen_categorized_blog() {
    $category_count = get_transient('twentyseventeen_categories');

    if (false === $category_count) {
        // Create an array of all the categories that are attached to posts.
        $categories = get_categories([
            'fields'     => 'ids',
            'hide_empty' => 1,
            // We only need to know if there is more than one category.
            'number'     => 2,
        ]);
        // Count the number of categories that are attached to the posts.
        $category_count = count($categories);

        set_transient('twentyseventeen_categories', $category_count);
    }

    return $category_count > 1;
}

/**
 * Flush out the transients used in twentyseventeen_categorized_blog.
 */
function twentyseventeen_category_transient_flusher() {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    delete_transient('twentyseventeen_categories');
}

add_action('edit_category', 'twentyseventeen_category_transient_flusher');
add_action('save_post', 'twentyseventeen_category_transient_flusher');

function get_was_tags_list() {
    $response = new Response();
    $tags = get_tags(['order' => 'ASC', 'lang' => pll_current_language()]);

    if ($tags) {
        $response->success();
        $response->setData($tags);
    } else {
        $response->failure();
        $response->setError("ОЙ ЧТО-ТО ПОШЛО НЕ ТАК :(");
    }

    echo $response->toJSON();

    die();
}

add_action('wp_ajax_get_was_tags_list', 'get_was_tags_list');
