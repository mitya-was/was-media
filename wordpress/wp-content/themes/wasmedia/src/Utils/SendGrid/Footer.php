<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:16
 */

namespace Utils\SendGrid;


class Footer implements \jsonSerializable {
    private
        $enable,
        $text,
        $html;

    public function setEnable($enable) {
        $this->enable = $enable;
    }

    public function getEnable() {
        return $this->enable;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }

    public function setHtml($html) {
        $this->html = $html;
    }

    public function getHtml() {
        return $this->html;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'enable' => $this->getEnable(),
                'text'   => $this->getText(),
                'html'   => $this->getHtml()
            ]
        );
    }
}