<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 10/5/2017
 * Time: 13:32
 */

function cptui_register_my_cpts_video() {

    /**
     * Post Type: Videos.
     */

    $labels = array(
        "name"          => __("Videos", ""),
        "singular_name" => __("Video", ""),
    );

    $args = array(
        "label"               => __("Videos", ""),
        "labels"              => $labels,
        "description"         => "",
        "public"              => true,
        "publicly_queryable"  => true,
        "show_ui"             => true,
        "show_in_rest"        => false,
        "rest_base"           => "",
        "has_archive"         => true,
        "show_in_menu"        => true,
        "exclude_from_search" => false,
        "capability_type"     => "post",
        "map_meta_cap"        => true,
        "hierarchical"        => false,
        "rewrite"             => array("slug" => "videos", "with_front" => true),
        "query_var"           => "videos",
        "menu_icon"           => "dashicons-format-video",
        "supports"            => array("title", "editor", "thumbnail", "custom-fields")
    );

    register_post_type("videos", $args);
}

add_action('init', 'cptui_register_my_cpts_video');