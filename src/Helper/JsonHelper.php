<?php

namespace Helper;

class JsonHelper
{
    /**
     * 整理json格式字符串数据
     * @param string $json json格式字符串数据
     * @param bool|false $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     */
    public function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
        $json = str_replace([
            "\n",
            "\r"
        ], "", $json);
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);
        $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/', '$1"$3":', $json);
        $json = preg_replace('/(,)\s*}$/', '}', $json);
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $json = json_decode($json, $assoc, $depth, $options);
        } else {
            if (version_compare(phpversion(), '5.3.0', '>=')) {
                $json = json_decode($json, $assoc, $depth);
            } else {
                $json = json_decode($json, $assoc);
            }
        }
        return $json;
    }

    /**
     * 判断$strJson是否是一个有效的json格式字符串
     * @param $strJson
     * @return bool
     */
    public function isValidJson($strJson) {
        json_decode($strJson);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * 去掉字符串中的斜线(单斜线和双斜线)
     * @param string $string
     * @return string
     */
    public static function removeslashes($string = '') {
        $string = implode("", explode("\\", $string));
        return stripslashes(trim($string));
    }

}