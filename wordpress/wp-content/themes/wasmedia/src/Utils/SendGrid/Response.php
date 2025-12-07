<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:09
 */

namespace Utils\SendGrid;


/**
 * Holds the response from an API call.
 */
class Response {
    /**
     * Setup the response data
     *
     * @param int   $status_code      the status code.
     * @param array $response_body    the response body as an array.
     * @param array $response_headers an array of response headers.
     */
    function __construct($status_code = null, $response_body = null, $response_headers = null) {
        $this->_status_code = $status_code;
        $this->_body = $response_body;
        $this->_headers = $response_headers;
    }

    /**
     * The status code
     *
     * @return integer
     */
    public function statusCode() {
        return $this->_status_code;
    }

    /**
     * The response body
     *
     * @return array
     */
    public function body() {
        return $this->_body;
    }

    /**
     * The response headers
     *
     * @return array
     */
    public function headers() {
        return $this->_headers;
    }
}