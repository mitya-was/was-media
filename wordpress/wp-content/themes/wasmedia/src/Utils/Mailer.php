<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/28/2017
 * Time: 15:00
 */

namespace Utils;

class Mailer {
    private $mailerSubject;

    private $emailTopLogo;
    private $emailTitle;
    private $emailDescription;
    private $emailBody;

    /** @var \WP_Post */
    private $globalPost;
    /** @var \WP_Post */
    private $currentPost;

    private $external;
    private $currentPostCustomTitle;
    private $currentPostCustomDescription;
    private $currentPostCustomImage;

    public function __construct() {
        /** @var \WP_Post $post */
        global $post;

        $this->globalPost = $post;

        if ($post) {
            $mailerTitles = get_field("mailer_titles", $post->ID);

            if (is_array($mailerTitles) && count($mailerTitles) > 0) {
                $this->mailerSubject = $mailerTitles["mailer_subject"];
            }

            $mailLogoOptions = get_field("email_top_logo", $post->ID);

            if (is_array($mailLogoOptions) && count($mailLogoOptions) > 0) {
                $this->emailTopLogo = ($mailLogoOptions["use_custom_top_logo"]) ?
                    custom_image_getter(
                        $mailLogoOptions["custom_top_logo_image"]["id"],
                        [
                            "i_thumb_name" => "header-mail"
                        ]
                    ) :
                    $this->getDefaultLogo();
            }

            $mailTitlesOptions = get_field("email_titles", $post->ID);

            if (is_array($mailTitlesOptions) && count($mailTitlesOptions) > 0) {
                $this->emailTitle = $mailTitlesOptions["email_title"];
                $this->emailDescription = $mailTitlesOptions["email_description"];
            }

            $mailPosts = get_field("email_post_block", $post->ID);

            if (is_array($mailPosts) && count($mailPosts) > 0) {
                $this->emailBody = $mailPosts;
            }
        }
    }

    public function getDefaultLogo() {
        return custom_image_getter(
            "https://was.media/wp-content/uploads/2017/09/default-mail-logo" . ((pll_current_language() === "ru") ? ".png" : "-uk.png"),
            [
                "i_thumb_name" => "header-mail"
            ]
        );
    }

    /**
     * @return string
     */
    public function getMailHeaderPart(): string {
        ob_start();
        require_once("Mailer/default-mailer-header.php");

        return ob_get_clean();
    }

    public function generateMailContent() {
        $mailBody = "";

        foreach ($this->emailBody as $postEntry) {
            $postOptions = $postEntry["post_entry"];
            $layout = ($postOptions["layout_format"]) ? "Mailer/default-mailer-body.php" : "Mailer/default-mailer-body-small.php";

            $this->currentPost = $postOptions["post"];
            $this->external = ($this->currentPost) ? null : $postOptions["external"];
            $this->currentPostCustomTitle = $this->getMailPostTitle($postOptions);
            $this->currentPostCustomDescription = $this->getMailPostDescription($postOptions);
            $this->currentPostCustomImage = $this->getMailPostImage($postOptions);

            ob_start();

            require($layout);

            $mailBody .= ob_get_clean();
        }

        return $mailBody;
    }

    private function getMailPostTitle($postOptions) {
        $result = "";
        /** @var \WP_Post $post */
        $post = $postOptions["post"];

        if ($postOptions["post_custom_title"] !== "") {
            $result = $postOptions["post_custom_title"];
        } else {
            $postTitle = get_the_title($post->ID);
            $postYTitle = get_post_meta($post->ID, "_yoast_wpseo_title", true);

            switch (true) {

                case !empty($postTitle):
                    $result = $postTitle;
                    break;

                case !empty($postYTitle):
                    $result = $postYTitle;
                    break;
            }
        }

        return $result;
    }

    private function getMailPostDescription($postOptions) {
        $result = "";
        /** @var \WP_Post $post */
        $post = $postOptions["post"];

        if ($postOptions["post_custom_description"] !== "") {
            $result = $postOptions["post_custom_description"];
        } else {
            $postDescription = get_the_excerpt($post->ID);
            $postYDescription = get_post_meta($post->ID, "_yoast_wpseo_metadesc", true);

            switch (true) {
                case !empty($postYDescription):
                    $result = strip_tags($postYDescription);
                    break;

                case !empty($postDescription):
                    $result = strip_tags($postDescription);
                    break;
            }
        }

        return $result;
    }

    private function getMailPostImage($postOptions) {
        /** @var \WP_Post $post */
        $result = null;
        $post = $postOptions["post"];
        $size = ($postOptions["layout_format"]) ? "full-mail" : "small-mail";

        if ($postOptions["post_custom_image_small"] || $postOptions["post_custom_image_full"]) {

            $customImage = ($postOptions["layout_format"]) ? $postOptions["post_custom_image_full"] : $postOptions["post_custom_image_small"];
            $result = custom_image_getter(
                $customImage["ID"],
                [
                    "i_thumb_name" => $size
                ]
            );

        }

        if ($post) {

            $result = custom_image_getter(
                get_post_thumbnail_id($post->ID),
                [
                    "i_thumb_name" => $size
                ]
            );

        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMailFooterPart(): string {
        ob_start();

        require_once("Mailer/default-mailer-footer.php");

        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function generatePreview(): string {
        ob_start();

        if ($this->isReadyToGo()) {
            require_once("Mailer/mailer-preview.php");
        } else {
            require_once("Mailer/mailer-preview-instruction.php");
        }

        return ob_get_clean();
    }

    /**
     * @return bool
     */
    public function isReadyToGo(): bool {
        return ($this->getMailerSubject() !== "" && count($this->getEmailBody()) > 0);
    }

    /**
     * @return string
     */
    public function generateUTMPermalink(): string {

        if (!$this->currentPost && $this->external) {
            return $this->external;
        }

        $permalink = get_the_permalink($this->getCurrentPost()->ID);
        $UTMSuffix = "?utm_source=newsletter&utm_medium=email&utm_campaign=";
        $UTMPostfixArray = explode("-", str_replace("/", "", substr(str_replace(pll_home_url(), "", $permalink), 11)));
        $UTMPostfix = (count($UTMPostfixArray) > 4) ?
            implode("-", array_slice($UTMPostfixArray, 0, 4)) :
            implode("-", $UTMPostfixArray);

        return $permalink . $UTMSuffix . $UTMPostfix;
    }

    /**
     * @return string
     */
    public function getMailerSubject(): string {
        return $this->mailerSubject;
    }

    /**
     * @return string
     */
    public function getEmailTopLogo(): string {
        return $this->emailTopLogo;
    }

    /**
     * @return string
     */
    public function getEmailTitle(): string {
        return $this->emailTitle;
    }

    /**
     * @return string
     */
    public function getEmailDescription(): string {
        return $this->emailDescription;
    }

    /**
     * @return array
     */
    public function getEmailBody(): array {
        return $this->emailBody;
    }

    /**
     * @return \WP_Post
     */
    public function getGlobalPost() {
        return $this->globalPost;
    }

    /**
     * @return \WP_Post | bool
     */
    public function getCurrentPost() {
        return $this->currentPost;
    }

    /**
     * @return string
     */
    public function getCurrentPostCustomTitle(): string {
        return $this->currentPostCustomTitle;
    }

    /**
     * @return string
     */
    public function getCurrentPostCustomDescription(): string {
        return $this->currentPostCustomDescription;
    }

    /**
     * @return string|null
     */
    public function getCurrentPostCustomImage(): ?string {
        return $this->currentPostCustomImage;
    }
}