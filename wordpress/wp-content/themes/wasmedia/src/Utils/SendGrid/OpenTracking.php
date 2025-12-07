<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:11
 */

namespace Utils\SendGrid;


class OpenTracking implements \jsonSerializable {
    private
        $enable,
        $substitution_tag;

    public function setEnable($enable) {
        $this->enable = $enable;
    }

    public function getEnable() {
        return $this->enable;
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
                'substitution_tag' => $this->getSubstitutionTag()
            ]
        );
    }
}