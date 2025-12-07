<?php

use Timber\Timber;

$context = Timber::get_context();

$after_posts = [];
$pre_posts = Timber::get_posts();

foreach ($pre_posts as $item) {

    if ($item && ($item->post_type == 'post' || $item->post_type == 'microformats')) {
        $after_posts[] = $item;
    }
}

$context['posts'] = $after_posts;

Timber::render("was-search.twig", $context, TWIG_CACHE_TIME);