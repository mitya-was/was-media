<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/22/2017
 * Time: 17:43
 */

use Utils\Mailer;
use Utils\Response;
use Utils\SendGrid;

function cptui_register_my_cpts_mailer() {

    /**
     * Post Type: Mailer.
     */

    $labels = array(
        "name"          => __("Mailer", ""),
        "singular_name" => __("Mailer", ""),
    );

    $args = array(
        "label"               => __("Mailer", ""),
        "labels"              => $labels,
        "description"         => "",
        "public"              => true,
        "publicly_queryable"  => true,
        "show_ui"             => true,
        "show_in_rest"        => false,
        "rest_base"           => "",
        "has_archive"         => false,
        "show_in_menu"        => true,
        "exclude_from_search" => true,
        "capability_type"     => "post",
        "map_meta_cap"        => true,
        "hierarchical"        => false,
        "rewrite"             => array("slug" => "mailer", "with_front" => false),
        "query_var"           => "mailer",
        "menu_icon"           => "dashicons-email-alt",
        "supports"            => array("custom-fields"),
    );

    register_post_type("mailer", $args);
}

add_action('init', 'cptui_register_my_cpts_mailer');

/**
 * @param $post_id
 */
function was_check_mailer($post_id) {

    if ($post_id == null || empty($_POST)) {
        return;
    }

    if (!isset($_POST['post_type']) || $_POST['post_type'] != 'mailer') {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        $post_id = wp_is_post_revision($post_id);
    }

    global $post;

    if (empty($post)) {
        $post = get_post($post_id);
    }

    /** wpdb $wpdb */
    global $wpdb;

    $date = date('l, d.m.Y, H:i:s', strtotime($post->post_date));
    $title = "Dispatch" . " " . $date;
    $where = array('ID' => $post_id);

    $wpdb->update($wpdb->posts, array('post_title' => $title, 'post_name' => sanitize_title($title)), $where);
}

add_action('save_post', 'was_check_mailer');

add_filter('single_template', function ($original) {
    global $post;

    if ($post && $post->post_type === "mailer") {
        $mailer = new Mailer();

        echo $mailer->generatePreview();

        die();
    }

    return $original;
}, 10);

add_filter('preview_post_link', function ($link) {
    global $post;

    if ($post && $post->post_type === "mailer") {

        if ($post->post_name === "") {
            was_check_mailer($post->ID);
        }

        $link = get_the_permalink($post->ID);
    }

    return $link;
});

function was_start_mailer() {
    /** @var \WP_Post $post */
    global $post;

    $response = new Response();
    /** @var stdClass $data */
    $data = json_decode(file_get_contents("php://input"));

    if ($data) {
        $list = null;
        $post = get_post($data->post_id);
        $mailer = new Mailer();

        switch (pll_current_language()) {

            case "ru" :
                $list = 1131381;
                break;

            case "uk" :
                $list = 1795078;
                break;
        }

        if ($mailer->isReadyToGo()) {
            $sg = new SendGrid('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');
            $html = $mailer->getMailHeaderPart() . $mailer->generateMailContent() . $mailer->getMailFooterPart();
            $request = [
                "title"                  => "WAS, " . date("d.m"),
                "subject"                => $mailer->getMailerSubject(),
                "sender_id"              => 126718,
                "list_ids"               => [
                    $list
                ],
                "categories"             => [
                    "Еженедельная рассылка"
                ],
                "suppression_group_id"   => 3995,
                "custom_unsubscribe_url" => "",
                "html_content"           => $html
            ];

            $sgResponse = $sg->client->makeRequest("POST", "https://api.sendgrid.com/v3/campaigns", $request);

            if ($sgResponse && $sgResponse->_status_code === 201) {
                /** @var stdClass $sgCampain */
                $sgCampain = json_decode((string)$sgResponse->_body);
                $time = strtotime(gmdate("Y/m/d H:i:s", strtotime("+10 minutes")));

                $sgCampaignResponse = $sg->client->makeRequest("POST", "https://api.sendgrid.com/v3/campaigns/{$sgCampain->id}/schedules", ["send_at" => $time]);

                if ($sgCampaignResponse && $sgCampaignResponse->_status_code === 201) {
                    $response->success();
                } else {
                    $response->failure();
                    $response->setData("Campaign has been created but not scheduled.");
                }

            } else {
                $response->failure();
                $response->setData("Campaign has not been created.");
            }
        }

        wp_reset_postdata();

    } else {
        $response->failure();
        $response->setData("Error while creating mailing template.");
    }

    echo $response->toJSON();

    die();
}

add_action('wp_ajax_was_start_mailer', 'was_start_mailer');