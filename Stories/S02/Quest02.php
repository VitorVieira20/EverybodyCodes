<?php

namespace Stories\S02;

ini_set('memory_limit', '1024M');

use SplQueue;

class Quest02
{
    private string $input1 = __DIR__ . '/../inputs/S02/Quest02/input1.txt';
    private string $input2 = __DIR__ . '/../inputs/S02/Quest02/input2.txt';
    private string $input3 = __DIR__ . '/../inputs/S02/Quest02/input3.txt';

    private array $balloons = [];
    private array $balloonsCircle = [];
    private array $fluffbolts = ["R", "G", "B"];


    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $this->balloons = array_values(str_split($lines[0]));
    }


    private function createBallonsCircle(int $n)
    {
        $this->balloonsCircle = [];

        for ($i = 0; $i < $n; $i++) {
            foreach ($this->balloons as $ballon) {
                $this->balloonsCircle[] = $ballon;
            }
        }
    }


    private function createBallonsCircleString(int $n): string
    {
        $string = "";
        for ($i = 0; $i < $n; $i++) {
            foreach ($this->balloons as $ballon) {
                $string .= $ballon;
            }
        }

        return $string;
    }


    public function solvePart1(): int
    {
        $this->parse($this->input1);

        $fluffboltIdx = 0;
        $rounds = 0;

        while (count($this->balloons) > 0) {
            $fluffbolt = $this->fluffbolts[$fluffboltIdx];

            while (count($this->balloons) > 0 && $this->balloons[0] === $fluffbolt) {
                array_shift($this->balloons);
            }

            if (count($this->balloons) > 0 && $this->balloons[0] !== $fluffbolt) {
                array_shift($this->balloons);
            }

            $rounds++;
            $fluffboltIdx = ($fluffboltIdx + 1) % count($this->fluffbolts);
        }

        return $rounds;
    }


    public function solvePart2(int $n = 100): int
    {
        $this->parse($this->input2);

        $this->createBallonsCircle($n);

        $fluffboltIdx = 0;
        $rounds = 0;

        while (count($this->balloonsCircle) > 0) {
            $fluffbolt = $this->fluffbolts[$fluffboltIdx];

            if ($this->balloonsCircle[0] === $fluffbolt) {
                if (count($this->balloonsCircle) % 2 == 0) {
                    $middleIdx = count($this->balloonsCircle) / 2;
                    unset($this->balloonsCircle[$middleIdx]);
                    $this->balloonsCircle = array_values($this->balloonsCircle);
                }
            }

            array_shift($this->balloonsCircle);

            $rounds++;
            $fluffboltIdx = ($fluffboltIdx + 1) % count($this->fluffbolts);
        }

        return $rounds;
    }


    public function solvePart3(int $n = 100000): int
    {
        $this->parse($this->input3);

        $balloonsCircle = $this->createBallonsCircleString($n);
        $half = strlen($balloonsCircle) / 2;

        $left = new SplQueue();
        $right = new SplQueue();

        for ($i = 0; $i < $half; $i++)
            $left->enqueue($balloonsCircle[$i]);
        for ($i = $half; $i < strlen($balloonsCircle); $i++)
            $right->enqueue($balloonsCircle[$i]);

        $rounds = 0;
        $fluffboltIdx = 0;


        while (!$left->isEmpty() || !$right->isEmpty()) {
            $fluffbolt = $this->fluffbolts[$fluffboltIdx];

            if (count($left) !== count($right)) {
                if (!$left->isEmpty()) {
                    $left->dequeue();
                }
            } else {
                if (!$left->isEmpty() && $left->bottom() === $fluffbolt) {
                    $left->dequeue();
                    if (!$right->isEmpty()) {
                        $right->dequeue();
                    }
                } else {
                    if (!$left->isEmpty()) {
                        $left->dequeue();
                    }
                    if (!$right->isEmpty()) {
                        $item = $right->dequeue();
                        $left->enqueue($item);
                    }
                }
            }

            $rounds++;
            $fluffboltIdx = ($fluffboltIdx + 1) % count($this->fluffbolts);
        }

        return $rounds;
    }
}