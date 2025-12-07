<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/8/2017
 * Time: 12:51
 */

function was_custom_sharer() {

    if (isset($_GET["fbresult"]) && !empty($_GET["fbresult"])) {
        $fbresult = sanitize_text_field($_GET["fbresult"]);
        
        foreach (explode("§", $fbresult) as $part) {
            $innerPart = explode("@", $part);
            if (count($innerPart) == 2) {
                $_GET[sanitize_key($innerPart[0])] = sanitize_text_field($innerPart[1]);
            }
        }

        $slug = isset($_GET["slug"]) ? esc_url_raw($_GET["slug"]) : '';
        $game = isset($_GET["game"]) ? sanitize_text_field($_GET["game"]) : '';
        $count = isset($_GET["count"]) ? absint($_GET["count"]) : 0;
        $v = isset($_GET["v"]) ? absint($_GET["v"]) : 0;
        
        if ($slug && $game && $count && $v) {
            generateShareContent();
        }
    }

    if (isset($_GET["share"]) && !empty($_GET["share"])) {
        $share = sanitize_text_field($_GET["share"]);
        
        foreach (explode("§", $share) as $part) {
            $innerPart = explode("@", $part);
            if (count($innerPart) == 2) {
                $_GET[sanitize_key($innerPart[0])] = sanitize_text_field($innerPart[1]);
            }
        }

        if ($share === "picture") {
            $n = isset($_GET["n"]) ? sanitize_text_field($_GET["n"]) : '';
            $d = isset($_GET["d"]) ? sanitize_text_field($_GET["d"]) : '';
            
            if ($n && $d) {
                generatePictureShareContent();
            }
        }
    }
}

add_action("init", "was_custom_sharer");

function generateShareContent() {
    global $post;

    $slug = isset($_GET['slug']) ? esc_url_raw($_GET['slug']) : '';
    if (!$slug) return;
    
    $post_id = url_to_postid($slug);
    if (!$post_id) return;
    
    $post = get_post($post_id);

    if ($post) {
        setup_postdata($post);

        add_filter('wpseo_opengraph_image', function () {
            return game_share_image("open-graph");
        }, 10, 1);
        add_filter('wpseo_twitter_image', function () {
            return game_share_image("twitter");
        }, 10, 1);

        add_filter('wpseo_opengraph_url', function () {
            return game_share_url("open-graph");
        }, 10, 1);
        add_filter('wpseo_canonical', function () {
            return game_share_url("canonical");
        }, 10, 1);

        wp_reset_postdata();
    }
}

function game_share_image($scope) {
    /** @var WP_Post $post */
    global $post;

    $url = "";
    $version = 1;
    $count = isset($_GET["count"]) ? absint($_GET["count"]) : 0;

    if (isset($_GET["v"]) && !empty($_GET["v"])) {
        $version = absint($_GET["v"]);
    }

    switch ($version) {

        case 1:
        case 2:
            $url = get_snippet_url_via_version($post, $count, $version);

            break;

        default:
            break;
    }

    if ($scope === "open-graph") {
        remove_filter('wpseo_opengraph_image', 'game_share_image', 10);
    }

    if ($scope === "twitter") {
        remove_filter('wpseo_twitter_image', 'game_share_image', 10);
    }

    return $url;
}

function get_snippet_url_via_version($post, $count, $version) {
    $url = "";
    $iteration = 0;

    if (have_rows('admin_post_layout', $post->ID)) {

        while (have_rows('admin_post_layout', $post->ID)) {
            the_row();

            if ($game_results = get_sub_field('game_results')) {

                foreach ($game_results as $value) {

                    if (($version == 1 && $count <= intval($value['results_number'])) || ($version == 2 && $count == $iteration)) {
                        $options = [
                            "i_thumb_name" => "og-image"
                        ];

                        if (!in_array("staticSnippetResult", (array)get_sub_field("game_options"))) {

                            $counter_text = (in_array("scores", (array)get_sub_field("game_options")) ||
                                in_array("categories", (array)get_sub_field("game_options"))) ?
                                "" :
                                $count . "/" . count((array)get_sub_field('game_items'));

                            $options = array_merge(
                                $options,
                                [
                                    "i_game" => [
                                        "i_txt"  => [
                                            "txt" => $counter_text
                                        ],
                                        "i_mark" => [
                                            "txt" => strip_tags($value['results_text'])
                                        ]
                                    ]
                                ]
                            );

                        }

                        $url = custom_image_getter($value['results_image']['ID'], $options);

                        break;
                    }

                    $iteration++;
                }
            }
        }
    }

    return $url;
}

function game_share_url($scope) {

    if ($scope === "open-graph") {
        remove_filter('wpseo_opengraph_url', 'game_share_url', 10);
    }

    if ($scope === "canonical") {
        remove_filter('wpseo_canonical', 'game_share_url', 10);
    }

    return get_site_url() . $_SERVER['REQUEST_URI'];
}

function generatePictureShareContent() {
    /** @var WP_Post $post */
    global $post;

    $post = get_post(url_to_postid($_SERVER["REQUEST_URI"]));

    if ($post) {
        setup_postdata($post);

        add_filter('wpseo_opengraph_image', 'custom_picture_share_image', 10, 1);

        add_filter('wpseo_opengraph_title', 'custom_wpseo_opengraph_title', 10, 1);

        add_filter('wpseo_opengraph_url', 'custom_picture_share_url', 10, 1);

        wp_reset_postdata();
    }
}

function custom_picture_share_image() {
    /** @var wpdb $wpdb */
    global $wpdb;

    if ($_GET["n"] === "imgix") {
        $url = base64_decode($_GET["d"]);

    } elseif ($_GET["n"] === "was") {
        $url = $_GET["d"];

    } else {
        $url = custom_image_getter(wp_get_attachment_image_url(get_post_thumbnail_id(), "full"));
//        $metaID = $wpdb->get_var($wpdb->prepare("SELECT b_w_posts.ID FROM b_w_posts WHERE b_w_posts.post_type = 'attachment' AND b_w_posts.guid LIKE %s;",
//            '%' . str_replace("-", "/", $wpdb->esc_like($_GET["d"])) . '/' . $wpdb->esc_like($_GET["n"]) . '%'));
//
//        if ($metaID) {
//            $url = custom_image_getter(wp_get_attachment_image_url($metaID, "full"));
//        }
    }

    remove_filter('wpseo_opengraph_image', 'custom_picture_share_image', 10);

    return $url;
}

function custom_wpseo_opengraph_title($title) {

    if (isset($_GET["fbtitle"]) && $_GET["fbtitle"] !== "") {
        $title = $_GET["fbtitle"];
    }

    remove_filter('wpseo_opengraph_title', 'custom_wpseo_opengraph_title', 10);

    return $title;
}

function custom_picture_share_url() {
    remove_filter('wpseo_opengraph_url', 'custom_picture_share_url', 10);

    return get_site_url() . $_SERVER['REQUEST_URI'];
}