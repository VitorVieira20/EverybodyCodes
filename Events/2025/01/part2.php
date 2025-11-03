<?php

function solvePart2($names, $instructions)
{
    $total = count($names);

    $currentName = 0;

    foreach ($instructions as $instruction) {
        $amount = (int) substr($instruction, 1);
        $isRight = $instruction[0] === 'R';

        if ($isRight) {
            $currentName = ($currentName + $amount) % $total;
        } else {
            $currentName = ($currentName - $amount) % $total;

            if ($currentName < 0) {
                $currentName += $total;
            }
        }
    }

    echo "Part 2: " . $names[$currentName] . PHP_EOL;
}