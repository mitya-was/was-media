<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:14
 */

namespace Utils\SendGrid;


class Personalization implements \jsonSerializable {
    private
        $tos,
        $ccs,
        $bccs,
        $subject,
        $headers,
        $substitutions,
        $custom_args,
        $send_at;

    public function addTo($email) {
        $this->tos[] = $email;
    }

    public function getTos() {
        return $this->tos;
    }

    public function addCc($email) {
        $this->ccs[] = $email;
    }

    public function getCcs() {
        return $this->ccs;
    }

    public function addBcc($email) {
        $this->bccs[] = $email;
    }

    public function getBccs() {
        return $this->bccs;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function addSubstitution($key, $value) {
        $this->substitutions[$key] = $value;
    }

    public function getSubstitutions() {
        return $this->substitutions;
    }

    public function addCustomArg($key, $value) {
        $this->custom_args[$key] = $value;
    }

    public function getCustomArgs() {
        return $this->custom_args;
    }

    public function setSendAt($send_at) {
        $this->send_at = $send_at;
    }

    public function getSendAt() {
        return $this->send_at;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'to'            => $this->getTos(),
                'cc'            => $this->getCcs(),
                'bcc'           => $this->getBccs(),
                'subject'       => $this->subject,
                'headers'       => $this->getHeaders(),
                'substitutions' => $this->getSubstitutions(),
                'custom_args'   => $this->getCustomArgs(),
                'send_at'       => $this->getSendAt()
            ]
        );
    }
}