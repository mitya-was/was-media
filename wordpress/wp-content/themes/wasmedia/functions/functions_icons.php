<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 10/13/2017
 * Time: 14:31
 */

/**
 * Add SVG definitions to the footer.
 */
function twentyseventeen_include_svg_icons() {
    // Define SVG sprite file.
    $svg_icons = get_parent_theme_file_path('/assets/images/svg-icons.inline.svg');
    // If it exists, include it.
    if (file_exists($svg_icons)) {
        require_once($svg_icons);
    }
}

add_action('wp_footer', 'twentyseventeen_include_svg_icons', 9999);

/**
 * Return SVG markup.
 *
 * @param array $args
 *
 * @type string $icon  Required SVG icon filename.
 * @type string $title Optional SVG title.
 * @type string $desc  Optional SVG description.
 *
 * @return string SVG markup.
 */
function twentyseventeen_get_svg($args = array()) {
    // Make sure $args are an array.
    if (empty($args)) {
        return __('Please define default parameters in the form of an array.', 'twentyseventeen');
    }
    // Define an icon.
    if (false === array_key_exists('icon', $args)) {
        return __('Please define an SVG icon filename.', 'twentyseventeen');
    }
    // Set defaults.
    $defaults = array(
        'icon'     => '',
        'title'    => '',
        'desc'     => '',
        'class'    => '',
        'datas'    => '',
        'style'    => '',
        'fallback' => false,
    );
    $unique_id = '';
    // Parse args.
    $args = wp_parse_args($args, $defaults);
    // Set aria hidden.
    $aria_hidden = ' aria-hidden="true"';
    // Set label.
    $aria_labelledby = '';

    if ($args['title']) {
        $aria_hidden = '';
        $unique_id = uniqid();
        $aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

        if ($args['desc']) {
            $aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
        }
    }

    if ($args['datas']) {
        $data_attribute = $args['datas'];
    }

    if ($args['style']) {
        $data_attribute = 'style=" ' . $args['style'] . '"';
    }

    // Begin SVG markup.
    $svg = '<svg class="icon icon-' . esc_attr($args['icon']) . ' ' . esc_attr($args['class']) . '" ' . esc_attr($args['style']) . ' ' . $aria_hidden . $aria_labelledby;
    $svg .= isset($data_attribute) ? $data_attribute : "";
    $svg .= ' role="img">';

    // Display the title.
    if ($args['title']) {
        $svg .= '<title id="title-' . $unique_id . '">' . esc_html($args['title']) . '</title>';

        // Display the desc only if the title is already set.
        if ($args['desc']) {
            $svg .= '<desc id="desc-' . $unique_id . '">' . esc_html($args['desc']) . '</desc>';
        }
    }

    /*
     * Display the icon.
     *
     * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
     *
     * See https://core.trac.wordpress.org/ticket/38387.
     */
    $svg .= ' <use href="#icon-' . esc_html($args['icon']) . '" xlink:href="#icon-' . esc_html($args['icon']) . '"></use> ';

    // Add some markup to use as a fallback for browsers that do not support SVGs.
    if ($args['fallback']) {
        $svg .= '<span class="svg-fallback icon-' . esc_attr($args['icon']) . '"></span>';
    }

    $svg .= '</svg>';

    return $svg;
}

/**
 * Display SVG icons in social links menu.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 *
 * @return string  $item_output The menu item output with social icon.
 */
function twentyseventeen_nav_menu_social_icons($item_output, $item, $depth, $args) {
    // Get supported social icons.
    $social_icons = twentyseventeen_social_links_icons();

    // Change SVG icon inside social links menu if there is supported URL.
    if ('social' === $args->theme_location) {
        foreach ($social_icons as $attr => $value) {
            if (false !== strpos($item_output, $attr)) {
                $item_output = str_replace($args->link_after, '</span>' . twentyseventeen_get_svg(array('icon' => esc_attr($value))), $item_output);
            }
        }
    }

    return $item_output;
}

add_filter('walker_nav_menu_start_el', 'twentyseventeen_nav_menu_social_icons', 10, 4);

/**
 * Add dropdown icon if menu item has children.
 *
 * @param  string $title The menu item's title.
 * @param  object $item  The current menu item.
 * @param  array  $args  An array of wp_nav_menu() arguments.
 *
 * @return string $title The menu item's title with dropdown icon.
 */
function twentyseventeen_dropdown_icon_to_menu_link($title, $item, $args) {

    if ('top' === $args->theme_location) {

        foreach ($item->classes as $value) {

            if ('menu-item-has-children' === $value || 'page_item_has_children' === $value) {
                $title = $title . twentyseventeen_get_svg(array('icon' => 'angle-down'));
            }
        }
    }

    return $title;
}

add_filter('nav_menu_item_title', 'twentyseventeen_dropdown_icon_to_menu_link', 10, 4);

/**
 * Returns an array of supported social links (URL and icon name).
 *
 * @return array $social_links_icons
 */
function twentyseventeen_social_links_icons() {
    // Supported social links icons.
    $social_links_icons = array(
        'facebook.com'    => 'facebook',
        'flickr.com'      => 'flickr',
        'plus.google.com' => 'google-plus',
        'instagram.com'   => 'instagram',
        'mailto:'         => 'envelope-o',
        'pinterest.com'   => 'pinterest-p',
        'skype.com'       => 'skype',
        'skype:'          => 'skype',
        'twitter.com'     => 'twitter',
        'vk.com'          => 'vk',
        'youtube.com'     => 'youtube',
    );

    /**
     * Filter Twenty Seventeen social links icons.
     *
     * @since Twenty Seventeen 1.0
     *
     * @param array $social_links_icons
     */
    return apply_filters('twentyseventeen_social_links_icons', $social_links_icons);
}