<?php

use Timber\Timber;
$context = Timber::get_context();

$index_posts_not_random = [
    'post_type'           => 'post',
    'posts_per_page'      => 9,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
    'post__not_in'        => get_option('sticky_posts'),
    'meta_query'	   => array(
        array(
            'key' => 'article_options',
            'value' => 'attach_if_random',
            'compare' => 'LIKE'
        ),
    ),
    'tax_query'           => [
        [
            'taxonomy' => 'post_format',
            'field'    => 'slug',
            'terms'    => [
                'post-format-image',
                'post-format-video'
            ],
            'operator' => 'NOT IN'
        ]
    ]
];

$context["index_posts"] = Timber::get_posts($index_posts_not_random);

if (count($context["index_posts"]) < 9){
    $index_posts_random = [
        'post_type'           => 'post',
        'posts_per_page'      => 9 - (int)count($context["index_posts"]),
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'post__not_in'        => get_option('sticky_posts'),
        'orderby'             => 'date',
        'order'               => 'DESC',
        'offset'              => mt_rand(0, 30),
        'tax_query'           => [
            [
                'taxonomy' => 'post_format',
                'field'    => 'slug',
                'terms'    => [
                    'post-format-image',
                    'post-format-video'
                ],
                'operator' => 'NOT IN'
            ]
        ]
    ];
    array_push($context["index_posts"], ...Timber::get_posts($index_posts_random));
}

$sticky_post = [
    'post_type'      => 'post',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
    'post__in'       => get_option('sticky_posts')
];

$index_posts_feature_not_random = [
    'post_type'      => 'post',
    'posts_per_page' => 2,
    'post_status'    => 'publish',
    'meta_query'	   => array(
        array(
            'key' => 'article_options',
            'value' => 'attach_if_random',
            'compare' => 'LIKE'
        ),
    ),
    'tax_query'      => [
        [
            'taxonomy' => 'post_format',
            'field'    => 'slug',
            'terms'    => [
                'post-format-image'
            ],
            'operator' => 'IN'
        ]
    ]
];

$context["index_posts_feature"] = Timber::get_posts($index_posts_feature_not_random);

if (count($context["index_posts_feature"]) < 2){

    $index_posts_feature_random = [
        'post_type'      => 'post',
        'posts_per_page' => 2 - (int)count($context["index_posts_feature"]),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => mt_rand(0, 30),
        'tax_query'      => [
            [
                'taxonomy' => 'post_format',
                'field'    => 'slug',
                'terms'    => [
                    'post-format-image'
                ],
                'operator' => 'IN'
            ]
        ]
    ];
    array_push($context["index_posts_feature"], ...Timber::get_posts($index_posts_feature_random));
}
$microformat_posts = [
    'post_type'      => 'microformats',
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    
];

$context['current_post_type'] = get_post_type();
$context["sticky_post"] = Timber::get_posts($sticky_post);
$context["microformat_posts"] = Timber::get_posts($microformat_posts);

Timber::render("was-index.twig", $context, TWIG_CACHE_TIME); ?>
