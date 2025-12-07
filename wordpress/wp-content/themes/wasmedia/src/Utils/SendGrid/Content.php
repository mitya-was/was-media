<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:12
 */

namespace Utils\SendGrid;


class Content implements \jsonSerializable {
    private
        $type,
        $value;

    public function __construct($type, $value) {
        $this->type = $type;
        $this->value = $value;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'type'  => $this->getType(),
                'value' => $this->getValue()
            ]
        );
    }
}