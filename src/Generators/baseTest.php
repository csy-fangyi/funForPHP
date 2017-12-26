<?php
function getFibonacci() {
    $i = 0;
    $k = 1;
    yield $k;
    while (true) {
        $k = $i + $k;
        $i = $k - $i;
        yield $k;
    }
}

$y = 0;

foreach (getFibonacci() as $fibonacci) {
    echo $fibonacci . "\n";
    $y++;
    if ($y > 30) {
        break;
    }
}

function printer() {
    $i = 1;
    while (true) {
        echo 'this is the yield ' . $i . "\n";
        echo 'receive: ' . yield . "\n";
        $i++;
    }
}

$printer = printer();
$printer->send('Hello');
$printer->send('world');

function printer2() {
    $i = 1;
    while(true) {
        echo 'this is the yield ' . (yield $i) . "\n";
        $i++;
    }
}

$printer = printer2();
var_dump($printer->send('first'));
var_dump($printer->send('second'));