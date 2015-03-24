<?php
/**
 * Created by PhpStorm.
 * User: EzyVA
 * Date: 3/19/2015
 * Time: 6:00 PM
 */

namespace Citrix;

class HttpResponse {
    private $http_code = 0;

    function __construct( $code = 0 ){
        $this->http_code = $code;
    }

    public function getCode() {
        return $this->http_code;
    }
    public function getText() {
        return $this->_statusText( $this->http_code );
    }

    private function _statusText($code = 0) {

        // List of HTTP status codes.
        $statuslist = array(
            '100' => 'Continue',
            '101' => 'Switching Protocols',
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '300' => 'Multiple Choices',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported'
        );

        // Caste the status code to a string.
        $code = (string)$code;

        // Determine if it exists in the array.
        if(array_key_exists($code, $statuslist) ) {

            // Return the status text
            return $statuslist[$code];

        } else {

            // If it doesn't exists, degrade by returning the code.
            return $code;

        }

    }
}