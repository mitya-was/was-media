<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:10
 */

namespace Utils\SendGrid;


class ReplyTo implements \jsonSerializable {
    private
        $email;

    public function __construct($email) {
        $this->email = $email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'email' => $this->getEmail()
            ]
        );
    }
}