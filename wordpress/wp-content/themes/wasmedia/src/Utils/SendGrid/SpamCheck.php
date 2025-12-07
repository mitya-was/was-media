<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:15
 */

namespace Utils\SendGrid;


class SpamCheck implements \jsonSerializable {
    private
        $enable,
        $threshold,
        $post_to_url;

    public function setEnable($enable) {
        $this->enable = $enable;
    }

    public function getEnable() {
        return $this->enable;
    }

    public function setThreshold($threshold) {
        $this->threshold = $threshold;
    }

    public function getThreshold() {
        return $this->threshold;
    }

    public function setPostToUrl($post_to_url) {
        $this->post_to_url = $post_to_url;
    }

    public function getPostToUrl() {
        return $this->post_to_url;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'enable'      => $this->getEnable(),
                'threshold'   => $this->getThreshold(),
                'post_to_url' => $this->getPostToUrl()
            ]
        );
    }
}