<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:16
 */

namespace Utils\SendGrid;


class BypassListManagement implements \jsonSerializable {
    private
        $enable;

    public function setEnable($enable) {
        $this->enable = $enable;
    }

    public function getEnable() {
        return $this->enable;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'enable' => $this->getEnable()
            ]
        );
    }
}
