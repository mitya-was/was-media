<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 2/21/2018
 * Time: 13:04
 */

function cptui_register_my_cpts_microformat() {

    /**
     * Post Type: Microformats.
     */

    $labels = [
        "name"          => pll__("Короткие истории"),
        "singular_name" => __("Microformat", ""),
    ];

    $args = [
        "label"               => __("Microformat", ""),
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
        "rewrite"             => ["slug" => "microformats", "with_front" => true],
        "query_var"           => "microformats",
        "menu_icon"           => "dashicons-exerpt-view",
        'taxonomies'          => ["microformat_tag"],
        "supports"            => ["title", "editor", "thumbnail", "custom-fields", "excerpt", "revisions"]
    ];

    register_post_type("microformats", $args);
}

add_action('init', 'cptui_register_my_cpts_microformat');

function create_microformats_tag_taxonomies() {
    $labels = [
        'name'                       => _x('Micro Tags', 'taxonomy general name'),
        'singular_name'              => _x('Micro Tag', 'taxonomy singular name'),
        'search_items'               => __('Search Micro Tags'),
        'popular_items'              => __('Popular Micro Tags'),
        'all_items'                  => __('All Micro Tags'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Micro Tag'),
        'update_item'                => __('Update Micro Tag'),
        'add_new_item'               => __('Add New Micro Tag'),
        'new_item_name'              => __('New Micro Tag Name'),
        'separate_items_with_commas' => __('Separate micro tags with commas'),
        'add_or_remove_items'        => __('Add or remove micro tags'),
        'choose_from_most_used'      => __('Choose from the most used micro tags'),
        'menu_name'                  => __('Micro Tags'),
    ];

    register_taxonomy('micro_tag', 'microformats', [
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'update_count_callback' => '_update_post_term_count',
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'query_var'             => 'micro_tag',
        'rewrite'               => ['slug' => 'micro-tag'],
    ]);
}

add_action('init', 'create_microformats_tag_taxonomies', 0);