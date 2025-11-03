<?php

function solvePart3($names, $instructions)
{
    $total = count($names);

    foreach ($instructions as $instruction) {
        $amount = (int) substr($instruction, 1);
        $isRight = $instruction[0] === 'R';

        if ($isRight) {
            $target = $amount % $total;
        } else {
            $target = (-$amount) % $total;
            if ($target < 0)
                $target += $total;
        }

        $temp = $names[0];
        $names[0] = $names[$target];
        $names[$target] = $temp;
    }

    echo "Part 3: " . $names[0] . PHP_EOL;
}
