<?php

require __DIR__ . '/../../vendor/autoload.php';

use Events\Y2025\Quest01;
use Events\Y2025\Quest02;
use Events\Y2025\Quest03;

$questsToSolve = [
    1 => [1, 2, 3],
    2 => [1, 2, 3],
    3 => [1, 2, 3],
];

foreach ($questsToSolve as $questNumber => $parts) {

    $className = sprintf("Events\\Y2025\\Quest%02d", $questNumber);

    if (!class_exists($className)) {
        echo "Quest $questNumber não existe!\n";
        continue;
    }

    $quest = new $className();

    echo "======== Quest " . sprintf("%02d", $questNumber) . " ========\n";

    foreach ($parts as $part) {
        $method = "solvePart$part";

        if (!method_exists($quest, $method)) {
            echo "Part $part does not exist!\n";
            continue;
        }

        $result = $quest->$method();
        echo "Part $part: $result\n";
    }

    echo PHP_EOL;
}
