<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:11
 */

namespace Utils\SendGrid;


class ClickTracking implements \jsonSerializable {
    private
        $enable,
        $enable_text;

    public function setEnable($enable) {
        $this->enable = $enable;
    }

    public function getEnable() {
        return $this->enable;
    }

    public function setEnableText($enable_text) {
        $this->enable_text = $enable_text;
    }

    public function getEnableText() {
        return $this->enable_text;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'enable'      => $this->getEnable(),
                'enable_text' => $this->getEnableText()
            ]
        );
    }
}