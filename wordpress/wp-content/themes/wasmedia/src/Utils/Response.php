<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 7/24/2017
 * Time: 17:49
 */

namespace Utils;

class Response {
    const DATA = "data";
    const ERROR = "error";
    const SUCCESS = "success";

    private $response;

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->response[self::DATA] = $data;
    }

    /**
     * @param mixed $errors
     */
    public function setError($errors) {
        $this->response[self::ERROR] = $errors;
    }

    public function success() {
        $this->response[self::SUCCESS] = true;
    }

    public function failure() {
        $this->response[self::SUCCESS] = false;
    }

    public function toJSON() {
        return json_encode($this->response);
    }
}