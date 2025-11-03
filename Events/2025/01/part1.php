<?php

function solvePart1($names, $instructions)
{
    $maxIndex = count($names) - 1;
    $current = 0;

    foreach ($instructions as $instruction) {
        $amount = (int) substr($instruction, 1);
        $isRight = $instruction[0] === 'R';

        if ($isRight) {
            $current = min($current + $amount, $maxIndex);
        } else {
            $current = max($current - $amount, 0);
        }
    }

    echo "Part 1: " . $names[$current] . PHP_EOL;
}
