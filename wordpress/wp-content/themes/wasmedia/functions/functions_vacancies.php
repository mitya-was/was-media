<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 9/25/2017
 * Time: 15:12
 */

function cptui_register_my_cpts_vacancies() {

    /**
     * Post Type: Vacancies.
     */

    $labels = array(
        "name"          => __("Vacancies", ""),
        "singular_name" => __("Vacancy", ""),
    );

    $args = array(
        "label"               => __("Vacancies", ""),
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
        "rewrite"             => array("slug" => "vacancies", "with_front" => true),
        "query_var"           => "vacancies",
        "menu_icon"           => "dashicons-hammer",
        "supports"            => array("title", "editor", "thumbnail", "excerpt", "custom-fields")
    );

    register_post_type("vacancies", $args);
}

add_action('init', 'cptui_register_my_cpts_vacancies');