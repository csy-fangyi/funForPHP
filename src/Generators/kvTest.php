<?php
/*
 * 下面每一行是用分号分割的字段组合，第一个字段将被用作键名。
 */
$input = <<<'EOF'
1;PHP;Likes dollar signs
2;Python;Likes whitespace
3;Ruby;Likes blocks
EOF;

function input_parser($input) {
    foreach (explode("\n", $input) as $line) {
        $fields = explode(';', $line);
        $id = array_shift($fields);
        yield $id => [array_shift($fields) => $fields];
    }
}

$generate = input_parser($input);

foreach ($generate as $key => $value) {
    echo $key . ' => ' . json_encode($value) . PHP_EOL;
}

?>