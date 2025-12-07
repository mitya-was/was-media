<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:17
 */

namespace Utils\SendGrid;


class SubscriptionTracking implements \jsonSerializable {
    private
        $enable,
        $text,
        $html,
        $substitution_tag;

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

    public function setSubstitutionTag($substitution_tag) {
        $this->substitution_tag = $substitution_tag;
    }

    public function getSubstitutionTag() {
        return $this->substitution_tag;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'enable'           => $this->getEnable(),
                'text'             => $this->getText(),
                'html'             => $this->getHtml(),
                'substitution_tag' => $this->getSubstitutionTag()
            ]
        );
    }
}