<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 2/16/2018
 * Time: 16:52
 */

use Utils\WASTimber;

function get_post_layout_variables() {
    return WASTimber::get_post_layout_parts();
}

function timber_content_parts_resolver($post_options) {
    $default_options = [
        'author_info'   => true,
        'date_info'     => true,
        'views_info'    => false,
        'content_part'  => 'content-none',
        'template_part' => 'index',
        'cover'         => true,
        'lazy'          => false,
        'thumb'         => 'thumbnail',
        'tags'          => 'article',
        'heading'       => 'h1'
    ];

    $custom_options = array_replace($default_options, $post_options);

    if (isset($custom_options['post']) && $custom_options['post'] instanceof Timber\Post) {
        setup_postdata($custom_options['post']);
    }

    $render = WASTimber::timber_content_parts_resolver($custom_options);

    wp_reset_postdata();

    return $render;
}

function get_content_part_by_post_type($post) {
    $result = 'content';

    if ($post && $post->post_type != "post") {
        $result = $post->post_type;
    }

    return $result;
}

function get_tags_navigation($taxonomy = ["post_tag", "micro_tag"])
{
    $lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
    $cache_key = 'was_tags_navigation_' . md5($lang . '|' . implode(',', (array)$taxonomy));

    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $result = [];
    $characters = [];

    switch (pll_current_language()) {

        case 'ru':
            $characters = [
                'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
                'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
            ];
            break;

        case 'uk':
            $characters = [
                'а', 'б', 'в', 'г', 'ґ', 'д', 'е', 'є', 'ж', 'з', 'и', 'і', 'ї', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
                'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я',
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
            ];
            break;
    }

    foreach ($characters as $character) {
        $tags = get_terms(
            [
                'taxonomy'   => $taxonomy,
                'name__like' => $character,
                'order'      => 'ASC'
            ]
        );

        if ($tags) {

            foreach ((array) $tags as $tag) {

                if (mb_strtolower(mb_substr($tag->name, 0, 1)) == $character) {
                    $lowered_tag_name = mb_strtolower($tag->name);

                    if (isset($result[$character][$lowered_tag_name])) {
                        $result[$character][$lowered_tag_name]->count += $tag->count;
                    } else {
                        $result[$character][$lowered_tag_name] = $tag;
                    }
                }
            }
        }
    }

    set_transient($cache_key, $result, HOUR_IN_SECONDS * 6);

    return $result;
}

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form_custom($post = 0)
{
    $post = get_post($post);
    $label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
    $output = '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" class="post-password-form form-inline" method="post">
	<div class="form-group"><input class="form-control form-control-lg" name="post_password" id="' . $label . '" type="password" placeholder="' . __('Password:') . '" size="20" /></div> <button class="search-submit btn btn-lg btn-brand btn-round-none" type="submit" name="Submit" />' . esc_attr_x('Enter', 'post password form') . '</button></form>
	';

    /**
     * Filters the HTML output for the protected post password form.
     *
     * If modifying the password field, please note that the core database schema
     * limits the password field to 20 characters regardless of the value of the
     * size attribute in the form input.
     *
     * @since 2.7.0
     *
     * @param string $output The password form HTML output.
     */
    return apply_filters('the_password_form_custom', $output);
}

function getAdvertLink() {
    $page_object = get_page_by_path('advertising');
    $page_id = $page_object ? $page_object->ID : 0;
    $page_translations_arr = pll_the_languages( array( 'raw' => 1, 'post_id' => $page_id, 'hide_if_no_translation' => 1) );
    $page_url = !empty($page_translations_arr) ? $page_translations_arr[pll_current_language( 'slug' )]['url'] : false;
    return $page_url;
}

function ampAnalyticsOpen() {
    echo '<!-- AMP Analytics --><script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
}

function ampAnalyticsClose() {
    echo '<!-- Google Tag Manager -->
<amp-analytics config="https://www.googletagmanager.com/amp.json?id=GTM-WM5ZTMM&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>';
}

add_action( 'amp_head_end', 'ampAnalyticsOpen', 10 );
add_action( 'apm_body_start', 'ampAnalyticsClose' );
