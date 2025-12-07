<?php
remove_all_actions('do_feed_rss2');
add_action('do_feed_rss2', 'was_feed_rss2', 10, 1);

function was_feed_rss2($for_comments) {
    $rss_template = get_template_directory() . '/feeds/was-rss2.php';

    if (file_exists($rss_template)) {
        load_template($rss_template);
    } else {
        do_feed_rss2($for_comments); // Call default function
    }
}

/* Add Rambler feed. */
function rambler_rss_init() {
    add_feed('rambler', 'rambler_rss');
}

add_action('init', 'rambler_rss_init');

function rambler_rss_content_type($content_type, $type) {

    if ('rambler' === $type) {
        return feed_content_type('rss2');
    }

    return $content_type;
}

add_filter('feed_content_type', 'rambler_rss_content_type', 10, 2);

function rambler_rss() {
    $rss_template = get_template_directory() . '/feeds/was-rss2-rambler.php';

    if (file_exists($rss_template)) {
        load_template($rss_template);
    } else {
        do_feed_rss2(false); // Call default function
    }
}

/* Add Dzen feed. */
function dzen_rss_init() {
    add_feed('dzen', 'dzen_rss');
}

add_action('init', 'dzen_rss_init');

function dzen_rss_content_type($content_type, $type) {

    if ('dzen' === $type) {
        return feed_content_type('rss2');
    }

    return $content_type;
}

add_filter('feed_content_type', 'dzen_rss_content_type', 10, 2);

function dzen_rss() {
    $rss_template = get_template_directory() . '/feeds/was-rss2-dzen.php';

    if (file_exists($rss_template)) {
        load_template($rss_template);
    } else {
        do_feed_rss2(false); // Call default function
    }
}