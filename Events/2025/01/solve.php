<?php

require __DIR__ . '/part1.php';
require __DIR__ . '/part2.php';
require __DIR__ . '/part3.php';

$input1 = __DIR__ . '/../inputs/01/input1.txt';
$input2 = __DIR__ . '/../inputs/01/input2.txt';
$input3 = __DIR__ . '/../inputs/01/input3.txt';

function parse($filePath)
{
    $lines = file($filePath, FILE_IGNORE_NEW_LINES);

    $names = explode(',', $lines[0]);
    $instructions = explode(',', $lines[2]);

    return [$names, $instructions];
}

// Part1
[$names, $instructions] = parse($input1);
solvePart1($names, $instructions);


// Part2
[$names, $instructions] = parse($input2);
solvePart2($names, $instructions);


// Part3
[$names, $instructions] = parse($input3);
solvePart3($names, $instructions);