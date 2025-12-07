<?php
use Timber\Timber;
use Utils\WASTimber;

global $post;
global $do_not_duplicate_top;

if ($post->post_status === 'future') {
    $options = (array) get_field('article_options', $post->ID);

    if (!in_array('show_as_published', $options)) {
        wp_safe_redirect("/404");
    }
}

if ($post->post_status == "private" &&
    is_user_logged_in() && current_user_can("partner") &&
    $post->post_author != get_current_user_id()) {

    wp_safe_redirect(home_url());
    die();
}

$template = 'was-single.twig';
$top_popular_posts = [];
$do_not_duplicate_top[] = $post->ID;
$related_materials = get_field('related_materials', $post->ID);

//for ($i = 0; $i < 9; $i++) {
//    $top_popular_posts[] = get_top_popular_posts();
//}

$context = Timber::get_context();
$type = null;

$context['post'] = Timber::get_post();
$context['cover'] = !is_article_options('no_cover_single', $post) && is_single() ?
    custom_image_getter(get_post_thumbnail_id($post->ID), ["i_thumb_name" => "main-cover"]) : false;
$context['heading'] = !is_article_options('no_cover_single', $post) && (is_single() || is_page()) ? 'display-4' :
    'display-3';
$context['related_materials'] = ($related_materials != "") ? Timber::get_posts($related_materials) : [];
$context['top_popular_posts'] = array_filter($top_popular_posts);

if ($post && $post->post_type == 'podcasts') {
    $context['podcast_text'] = get_field('custom_text');
    $context['podcasts'] = get_field('sound_link');
}

if ($post && $post->post_type == 'microformats') {
    $micro_main_title = pll__('Короткие истории');
    $micro_read_more = pll__('Читать дальше');
    $current_language = pll_current_language();
    $current_queried_object = get_queried_object();
    $current_tag = ($current_queried_object instanceof WP_Term) ? $current_queried_object : null;
    $micro_url = home_url() . "/microformats";
    $current_tag_exclude = ($current_tag) ? $current_tag->term_id : null;

    $context['archive_title'] = $micro_main_title;
    $context['all_micro_url'] = $micro_url;
}

if ($post && post_password_required($post)) {
    $type = 'protected';
}

function add_custom_game_props(){
    global $post;
    global $wpseo_og;
    if ($post && is_game_options('custom', $post )){
        $wpseo_og_image = new WPSEO_OpenGraph_Image('https://was.imgix.net/wp-content/uploads/2017/08/ostarkizm_was_07.jpg', $wpseo_og);
        $wpseo_og_image->show();
    }
}

Timber::render(WASTimber::timber_single_templates_resolver($post, $type), $context, TWIG_CACHE_TIME);

//--------------------------------------------------------------------------------------

function get_top_popular_posts() {
    /** wpdb $wpdb */
    global $wpdb;
    /** WP_Post $post */
    global $post;
    global $do_not_duplicate_top;

    $result = false;
    $exclude_language = ["-1"];
    $languages = pll_languages_list();
    $current_language = pll_current_language();

    if (($key = array_search($current_language, $languages)) !== false) {
        unset($languages[$key]);
    }
	$exclude_lang_posts = query_posts([
		'lang'        => implode($languages, ","),
		'post_status' => '-1',
		'showposts'   => '-1',
		'post_type'   => ['post', 'page']
	]);

    /** @var WP_Post $lang_post */
    foreach ($exclude_lang_posts as $exclude_lang_post) {
        array_push($exclude_language, $exclude_lang_post->ID);
    }

    wp_reset_query();

    $top_sql = "SELECT `postid` FROM `{$wpdb->prefix}kento_pvc` WHERE `postid` NOT IN ( '" .
        implode($do_not_duplicate_top, "', '") . "', '" . implode($exclude_language, "', '") .
        "') ORDER BY `count` DESC LIMIT 1";
    $top_results = $wpdb->get_col($top_sql);

    if (isset($top_results) && isset($top_results[0])) {
        $top_post_id = intval($top_results[0]);

        $post = get_post($top_post_id, OBJECT);

        $do_not_duplicate_top[] = $top_post_id;

        $result = $post;

        wp_reset_postdata();
    }

    return $result;
}
?>