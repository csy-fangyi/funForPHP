<?php

class HttpUtils
{
    public static function doGet($url, $data = '', $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

class ICP
{
    private $URL = 'http://www.sojson.com/api/beian/';

    public function queryICP($domain) {
        $url = $this->URL . $domain;

        return HttpUtils::doGet($url);
    }
}


$ICP = new ICP();
print_r($ICP->queryICP('www.chuangcache.com'));
sleep(3);
print_r($ICP->queryICP('www.baidu.com'));
sleep(3);
print_r($ICP->queryICP('www.sina.com'));