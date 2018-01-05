<?php

require "Curl.class.php";

class HttpUtils
{
    /**
     * doPostA
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return string
     */
    static function doPostA($url, $data = [], $header = ['Content-Type' => 'application/json']) {
        try {
            $curl = new \utils\Curl();
            if (!empty($header)) {
                foreach ($header as $key => $value) {
                    $curl->setHeader($key, $value);
                }
            }
            $curl->post($url, $data);
            if ($curl->error) {
                return null;
            } else {
                return $curl->response;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * doGetA
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return string
     */
    static function doGetA($url, $data = [], $header = []) {
        try {
            $curl = new \utils\Curl();
            if (!empty($header)) {
                foreach ($header as $key => $value) {
                    $curl->setHeader($key, $value);
                }
            }
            $curl->get($url, $data);
            if ($curl->error) {
                return null;
            } else {
                return $curl->response;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}