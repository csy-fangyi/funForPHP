<?php

require "HttpUtils.php";

class ICP
{
    public static function queryICP($domain) {
        $url = 'http://www.sojson.com/api/beian/' . $domain;
        return HttpUtils::doGetA($url);
    }
}

echo json_encode(ICP::queryICP('www.chuangcache.com')) . PHP_EOL;