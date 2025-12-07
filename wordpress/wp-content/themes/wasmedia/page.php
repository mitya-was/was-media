<?php

use Timber\Timber;

global $post;

$template = null;

if ($post && $post->post_name) {
    $file = get_template_directory() . '/views/pages/' . $post->post_name . '.twig';

    if (file_exists($file)) {
        $template = 'pages/' . $post->post_name . '.twig';
    }
}

wp_reset_query();

$context = Timber::get_context();

if ($post && $post->post_name == "newsletter-subscribe") {

    if (isset($_COOKIE['subs_status']) && $_COOKIE['subs_status'] === "success") {
        $display_none_style = true;
    } else {
        $display_none_style = false;
    }

    $recent_posts = wp_get_recent_posts(['post_status' => 'publish', 'numberposts' => '1', 'suppress_filters' => false]);

    $context['display_none_style'] = $display_none_style;
    $context['recent_posts'] = $recent_posts;
}

$page_objact = get_page_by_path('advertising');
$page_id = $page_objact ? $page_objact->ID : 0;
$page_translation = pll_the_languages( array( 'raw' => 1, 'post_id' => $page_id, 'hide_current' => 1, 'hide_if_no_translation' => 1) );
$current_lang = pll_current_language( 'slug' ) === 'ru' ? 'uk' : 'ru';
$context['pll_advert_post_url'] = $page_translation[$current_lang]['url'];

$context['post'] = $post;
$context['heading'] = !is_article_options('no_cover_single', $post) && (is_single() || is_page()) ? 'display-4' : 'display-3';
$context['template'] = $template;

Timber::render("was-page.twig", $context, TWIG_CACHE_TIME);