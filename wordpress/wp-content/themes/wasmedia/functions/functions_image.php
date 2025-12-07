<?php

use Utils\ImgixBuilder;

//Add custom image sizes
add_image_size('format-image-cover', 680, 510, true);
add_image_size('main-cover', 1041, 510, true);
add_image_size('popular-thumbs', 162, 267, true);
add_image_size('header-banner', 1920, 130, false);
add_image_size('x-large', 1041);
add_image_size('og-micro', 314, 314, true);
add_image_size('og-image', 1200, 630, true);
add_image_size('header-mail', 600, 114, true);
add_image_size('full-mail', 600, 294, true);
add_image_size('small-mail', 600, 294, true);
add_image_size('thumbs', 98, 70, true);

/**
 * Options keys:
 *
 * i_thumb_name - string name of custom image size,
 * i_sizes - array of sizes for CDN only crop (has priority over thumbnail crop) - [`w` => .., `h` => ..],
 * i_crop = bool value to enable|disable CDN crop according to image crop via admin area (default is true),
 * i_game = array of options to build game snippet [`i_txt` => [`txt` => .., ...], `i_mark` => [`txt` => .., ...]]
 *          & Imgix txt | mark options with corresponding name-keys (`txtfont`, `txtclr`, ...) could be passed to this
 *          array with overriding default values
 *
 * & Imgix general options with corresponding name-keys (`w`, `h`, ...) could be passed to this array
 * with overriding default values
 *
 * If function return 404 local src for image
 * check media thumbnails (run Thumbnails Regenerate plugin)
 *
 * @param       $image   - ID or URL Or Image Array (ID is preferred)
 * @param array $options - Array of options
 *
 * @return string
 */
function custom_image_getter($image, $options = null) {
    return ImgixBuilder::buildImage($image, $options);
}

function custom_src_set_getter($image, $options = null) {
    return ImgixBuilder::buildSrcSet($image, $options);
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function get_image_sizes() {
    global $_wp_additional_image_sizes;

    $sizes = [];

    foreach (get_intermediate_image_sizes() as $_size) {

        if (in_array($_size, ['thumbnail', 'medium', 'medium_large', 'large'])) {
            $sizes[$_size]['width'] = get_option("{$_size}_size_w");
            $sizes[$_size]['height'] = get_option("{$_size}_size_h");
            $sizes[$_size]['crop'] = (bool) get_option("{$_size}_crop");

        } elseif (isset($_wp_additional_image_sizes[$_size])) {
            $sizes[$_size] = [
                'width'  => $_wp_additional_image_sizes[$_size]['width'],
                'height' => $_wp_additional_image_sizes[$_size]['height'],
                'crop'   => $_wp_additional_image_sizes[$_size]['crop'],
            ];
        }
    }

    return $sizes;
}

/**
 * Get size information for a specific image size.
 *
 * @uses   get_image_sizes()
 *
 * @param  string $size The image size for which to retrieve data.
 *
 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
 */
function get_image_size($size) {
    $sizes = get_image_sizes();

    if (isset($sizes[$size])) {
        return $sizes[$size];
    }

    return false;
}

/**
 * Get the width of a specific image size.
 *
 * @uses   get_image_size()
 *
 * @param  string $size The image size for which to retrieve data.
 *
 * @return bool|string $size Width of an image size or false if the size doesn't exist.
 */
function get_image_width($size) {

    if (!$size = get_image_size($size)) {
        return false;
    }

    if (isset($size['width'])) {
        return $size['width'];
    }

    return false;
}

/**
 * Get the height of a specific image size.
 *
 * @uses   get_image_size()
 *
 * @param  string $size The image size for which to retrieve data.
 *
 * @return bool|string $size Height of an image size or false if the size doesn't exist.
 */
function get_image_height($size) {

    if (!$size = get_image_size($size)) {
        return false;
    }

    if (isset($size['height'])) {
        return $size['height'];
    }

    return false;
}

//new cropper hook
function build_new_file_mic($data) {
    $uni = substr(uniqid(), 0, 3);
    $path = pathinfo($data[0]);
    $dst_file_res = $path['dirname'] . '/' . $path['filename'] . '-' . $uni . '.' . $path['extension'];
    $res[] = $dst_file_res;
    $path = pathinfo($data[1]);
    $dst_file_url_res = $path['dirname'] . '/' . $path['filename'] . '-' . $uni . '.' . $path['extension'];
    $res[] = $dst_file_url_res;

    return $res;
}

add_filter('mic_dst_file_path', 'build_new_file_mic');

function my_gv_wpseo_opengraph_image($img) {

    if (IS_CDN_ENABLED) {
        $img = str_replace(site_url(), ImgixBuilder::makeCDNSiteUrl(), $img);
    }

    return $img;
}

add_filter('wpseo_opengraph_image', 'my_gv_wpseo_opengraph_image', 9, 1);

function was_opengraph_image_size() {
    return 'og-image';
}

add_filter('wpseo_opengraph_image_size', 'was_opengraph_image_size');
add_filter('wpseo_twitter_image_size', 'was_opengraph_image_size');

function ml_media_downsize($out, $id, $size) {
    $imagedata = wp_get_attachment_metadata($id);

    if (is_array($imagedata) && isset($imagedata['sizes']['og-image'])) {
        return false;
    }

    global $_wp_additional_image_sizes;

    if (!isset($_wp_additional_image_sizes['og-image'])) {
        return false;
    }

    if (!$resized = image_make_intermediate_size(
        get_attached_file($id),
        $_wp_additional_image_sizes['og-image']['width'],
        $_wp_additional_image_sizes['og-image']['height'],
        $_wp_additional_image_sizes['og-image']['crop']
    )) {
        return false;
    }

    $imagedata['sizes']['og-image'] = $resized;

    wp_update_attachment_metadata($id, $imagedata);

    $att_url = wp_get_attachment_url($id);

    return [dirname($att_url) . '/' . $resized['file'], $resized['width'], $resized['height'], true];
}
add_filter('image_downsize', 'ml_media_downsize', 10, 3);


function lazyload_image( $html ) {
    $html = str_replace( 'src=', 'data-src=', $html );
    return $html;
}