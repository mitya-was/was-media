<?php

use Timber\Timber;

$heading = post_type_archive_title('', false) ?: single_cat_title('', false) ?: get_the_author();
$current_queried_object = get_queried_object();
$current_tag = ($current_queried_object instanceof WP_Term) ? $current_queried_object : null;
$current_post_type = ($current_tag || is_author()) ? "post" : get_post_type();
$current_post_type = (($current_tag && $current_tag->taxonomy === "micro_tag") && !is_author()) ? "microformats" : $current_post_type;
$items_count = $current_post_type == "microformats" ? 5 : 11;
$read_more_text = pll__("Больше материалов");
$current_language = pll_current_language();

$post_filters = [
    'lang'           => $current_language,
    'post_type'      => $current_post_type ? 'post' : null,
    'post_status'    => 'publish',
    'posts_per_page' => $items_count
];

$context = Timber::get_context();
$context['archive_title'] = $heading;

if ($current_post_type == "microformats" && !$current_tag) {
    $post_filters['post_type'] = 'microformats';

    $read_more_text = pll__('Читать дальше');
    $micro_main_title = pll__('Короткие истории');

    $context['archive_title'] = $micro_main_title;

} elseif (is_author() && isset($current_queried_object->data) && isset($current_queried_object->data->user_nicename)) {
    $post_filters['author_name'] = $current_queried_object->data->user_nicename;
}

if ($current_tag) {
    $post_filters['tax_query'] = [
        [
            'taxonomy' => 'post_tag',
            'field'    => 'name',
            'terms'    => $current_tag->name
        ]
    ];

    $micro_filters = [
        'lang'           => $current_language,
        'post_type'      => 'microformats',
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'micro_tag',
                'field'    => 'name',
                'terms'    => $current_tag->name
            ]
        ],
        'posts_per_page' => 21
    ];

    $context['posts'] = Timber::get_posts($post_filters);
    $context['micro_posts'] = Timber::get_posts($micro_filters);
} else {
    $context['posts'] = Timber::get_posts($post_filters);
}


// ToDo: do it normal
foreach ($context['posts'] as &$post) {
	$post->cover = !is_article_options('no_cover_single', $post) ?
		custom_image_getter(get_post_thumbnail_id($post->ID), ["i_thumb_name" => "main-cover"]) : false;
}

$context['current_post_type'] = $current_post_type;
$context['current_tag'] = $current_tag;
$context['items_count'] = $items_count;
$context['read_more'] = $read_more_text;

Timber::render("was-archive.twig", $context, TWIG_CACHE_TIME);