<?php

use Utils\WASTimber;

// Theme loading timestamp for debugging
error_log('WAS Theme functions.php loaded at ' . date('Y-m-d H:i:s'));

require_once __DIR__ . '/vendor/autoload.php';

WASTimber::initialize();

include 'functions/functions_error_handler.php';
include 'functions/functions_translating.php';
include 'functions/functions_utils.php';
include 'functions/functions_maintenance.php';
include 'functions/functions_admin.php';
include 'functions/functions_mailer.php';
include 'functions/functions_rss.php';
include 'functions/functions_theme.php';
include 'functions/functions_plugins.php';
include 'functions/functions_image.php';
include 'functions/functions_icons.php';
include 'functions/functions_banners.php';
include 'functions/functions_sidebars.php';
include 'functions/functions_posts.php';
include 'functions/functions_games.php';
include 'functions/functions_sharer.php';
include 'functions/functions_lottery.php';
include 'functions/functions_vacancies.php';
include 'functions/functions_videos.php';
include 'functions/functions_tm_bot.php';
include 'functions/functions_podcast.php';
include 'functions/functions_microformat.php';
include 'functions/functions_template.php';
include 'functions/functions_lacoste.php';
include 'functions/functions_performance.php';

// Critical function verification for production debugging
if (!function_exists('was_count_block')) {
    error_log('CRITICAL ERROR: was_count_block function not loaded! Check functions_posts.php');
    
    // Emergency fallback to prevent fatal errors
    function was_count_block($mode = 'off', $post = null) {
        error_log('WARNING: Using emergency fallback for was_count_block');
        return '<span class="post-views">0</span>';
    }
} else {
    error_log('SUCCESS: was_count_block function loaded correctly');
}