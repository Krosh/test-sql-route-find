<?php

$graph = [];
$file = fopen(__DIR__ . $fileName, 'r');

$skip = fgets($file);
while ($string = fgets($file)) {
    $data = explode(" ", $string);
    if (!isset($graph[$data[0]])) {
        $graph[$data[0]] = [];
    }
    if (!isset($graph[$data[0]][$data[1]])) {
        $graph[$data[0]][$data[1]] = [];
    }
    $graph[$data[0]][$data[1]] = (int)$data[2];
}