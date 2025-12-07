<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 10/13/2017
 * Time: 14:24
 */

/** @param $plugins
 *
 * @return mixed
 */
function was_filter_plugin_updates($plugins) {

    if ($plugins && isset($plugins->response['amp/amp.php'])) {
        unset($plugins->response['amp/amp.php']);
    }

    return $plugins;
}

add_filter('site_transient_update_plugins', 'was_filter_plugin_updates');

/**
 * Disable AMP for specified post IDs
 *
 * @param $skip
 *
 * @return bool
 */
function isa_skip_posts($skip) {

    if (in_category(array('games'))) {
        $skip = true;
    }

    return $skip;
}

add_filter('amp_skip_post', 'isa_skip_posts', 10, 3);

/**
 * Do not load Merriweather Google fonts on AMP pages
 */
function isa_remove_amp_google_fonts() {
    remove_action('amp_post_template_head', 'amp_post_template_add_fonts');
}

add_action('amp_post_template_head', 'isa_remove_amp_google_fonts', 2);

/**
 * Include AMP component scripts.
 *
 * Be sure to only register the scripts for the extensions
 * that you really need to reduce page load times.
 *
 * @filter amp_post_template_data
 *
 * @param array $data Input from filter.
 *
 * @return array
 */
function xwp_amp_component_scripts($data) {
    global $post;

    $custom_component_scripts = array(
        'amp-iframe'       => 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js',
        'amp-carousel'     => 'https://cdn.ampproject.org/v0/amp-carousel-0.1.js',
        'amp-bind'         => 'https://cdn.ampproject.org/v0/amp-bind-0.1.js',
        'amp-sidebar'      => 'https://cdn.ampproject.org/v0/amp-sidebar-0.1.js',
        'amp-social-share' => 'https://cdn.ampproject.org/v0/amp-social-share-0.1.js',
        'amp-lightbox'     => 'https://cdn.ampproject.org/v0/amp-lightbox-0.1.js'
    );

    if (!isElementExists("slider", $post)) {
        unset($custom_component_scripts["amp-carousel"], $custom_component_scripts["amp-bind"]);
    }

    if (!isElementExists("iFrame", $post)) {
        unset($custom_component_scripts["amp-iframe"]);
    }

    $data['amp_component_scripts'] = array_merge($data['amp_component_scripts'], $custom_component_scripts);

    return $data;
}

function isElementExists($ACFElement, $post) {
    $result = false;
    $ACFPostLayout = get_field("admin_post_layout", $post->ID);

    foreach ($ACFPostLayout as $layout) {

        if (isset($layout["acf_fc_layout"]) && $layout["acf_fc_layout"] === "add_gallery") {

            switch ($ACFElement) {
                case "slider":

                    if (isset($layout["gallery_options"]) && is_array($layout["gallery_options"]) && in_array("slider", $layout["gallery_options"])) {
                        $result = true;
                    }
                    break;

                case "iFrame":

                    if (isset($layout["main_iframe"]) && is_array($layout["main_iframe"])) {
                        $result = true;
                    }
                    break;

                default:
                    break;
            }
        }
    }

    return $result;
}

add_filter('amp_post_template_data', 'xwp_amp_component_scripts');


function redirection_to_editor() {
    return 'edit_posts';
}

add_filter('redirection_role', 'redirection_to_editor');

add_action('admin_menu', function () {

    if (current_user_can('wpml_manage_string_translation') && function_exists('PLL') && !is_admin()) {
        add_menu_page(__('Strings translations', 'polylang'), __('String Translations', 'polylang'), 'wpml_manage_string_translation', 'mlang_strings', array(PLL(), 'languages_page'), 'dashicons-translation');
    }
});