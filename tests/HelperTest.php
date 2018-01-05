<?php

require "../src/Helper/FileHelper.php";

$files = \Helper\FileHelper::deepScanDir('/Data/logs/');
print_r($files);

require "../src/Helper/StringHelper.php";
$str = '你好吗？我很好，谢谢。你呢？How are you? Fine, thank you. And you?';
echo $str . PHP_EOL;
echo \Helper\StringHelper::truncate_utf8_string($str, 8) . PHP_EOL;

echo \Helper\StringHelper::utf8_gb2312($str) . PHP_EOL;

echo \Helper\StringHelper::cn_substr($str, 54) . PHP_EOL;