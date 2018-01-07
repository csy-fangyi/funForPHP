<?php
// 匿名函数
$closureFunc = function ($str) {
    echo $str . PHP_EOL;
};

$closureFunc("closureFunc");

// 闭包
echo '----closureFunc1----' . PHP_EOL;
function closureFunc1() {
    $func = function () {
        echo "closureFunc1" . PHP_EOL;
    };
    $func();
}

closureFunc1();

echo '----closureFunc2----' . PHP_EOL;
function closureFunc2() {
    $num = 2;
    $func = function () use ($num) {
        echo $num . PHP_EOL;
    };
    $func();
}

closureFunc2();

echo '----closureFunc3----' . PHP_EOL;
function closureFunc3() {
    $num = 3;
    $func = function () use ($num) {
        echo $num . PHP_EOL;
    };
    return $func;
}

$func = closureFunc3();
$func();

echo '----closureFunc4----' . PHP_EOL;
function closureFunc4() {
    $num = 4;
    $func = function ($str) use ($num) {
        echo $num . PHP_EOL . $str . PHP_EOL;
    };
    return $func;
}

$func = closureFunc4();
$func("hello, closure4");

echo '----closureFunc5----' . PHP_EOL;
function closureFunc5() {
    $num = 5;
    $func = function () use (&$num) {
        $num++;
        echo $num . PHP_EOL;
    };
    echo $num . PHP_EOL;
    return $func;
}

$func = closureFunc5();
$func();
$func();

echo '----callFunc----' . PHP_EOL;
function callFunc($func) {
    $func("argv");
}

callFunc(function ($str) {
    echo $str . PHP_EOL;
});
