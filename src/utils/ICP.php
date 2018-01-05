<?php

require "HttpUtils.php";

class ICP
{
    private $URL = 'http://www.sojson.com/api/beian/';

    public function queryICP($domain) {
        $url = $this->URL . $domain;
        return HttpUtils::doGetA($url);
    }
}

$ICP = new ICP();
echo json_encode($ICP->queryICP('www.chuangcache.com')) . PHP_EOL;