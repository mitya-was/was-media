<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 2/16/2018
 * Time: 13:23
 */

function cptui_register_my_cpts_podcast() {

    /**
     * Post Type: Podcasts.
     */

    $labels = array(
        "name"          => pll__("Подкаст"),
        "singular_name" => __("Podcast", ""),
    );

    $args = array(
        "label"               => __("Podcast", ""),
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
        "rewrite"             => array("slug" => "podcasts", "with_front" => true),
        "query_var"           => "podcasts",
        "menu_icon"           => "dashicons-controls-volumeon",
        'taxonomies'          => array('post_tag'),
        "supports"            => array("title", "editor", "thumbnail", "custom-fields")
    );

    register_post_type("podcasts", $args);
}

add_action('init', 'cptui_register_my_cpts_podcast');