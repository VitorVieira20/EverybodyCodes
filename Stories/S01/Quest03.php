<?php

namespace Stories\S01;


class Quest03
{
    private string $input1 = __DIR__ . '/../inputs/S01/Quest03/input1.txt';
    private string $input2 = __DIR__ . '/../inputs/S01/Quest03/input2.txt';
    private string $input3 = __DIR__ . '/../inputs/S01/Quest03/input3.txt';

    private array $snails = [];

    public function __construct()
    {
    }


    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->snails = [];

        foreach ($lines as $line) {
            preg_match('/x=(\d+) y=(\d+)/', $line, $matches);

            $x = (int) $matches[1];
            $y = (int) $matches[2];

            $this->snails[] = ['x' => $x, 'y' => $y];
        }
    }


    private function formula(int $x, int $y): int
    {
        return $x + 100 * $y;
    }


    private function predictPosition(int $x, int $y, int $days): int
    {
        $discSize = $x + $y - 1;
        $offset = $x - 1;
        $finalOffset = ($offset + $days) % $discSize;

        $newX = 1 + $finalOffset;
        $newY = $discSize - $finalOffset;

        return $this->formula($newX, $newY);
    }


    private function getCycle(int $x, int $y): int
    {
        return $x + $y - 1;
    }


    private function getOffset(int $x): int
    {
        return $x - 1;
    }


    private function gcd(int $a, int $b): int
    {
        return $b === 0 ? $a : $this->gcd($b, $a % $b);
    }


    private function lcm(int $a, int $b): int
    {
        return (int) (($a * $b) / $this->gcd($a, $b));
    }


    private function allAlignedAtDay(int $day): bool
    {
        foreach ($this->snails as $snail) {
            $cycle = $this->getCycle($snail['x'], $snail['y']);
            $offset = $this->getOffset($snail['x']);

            if (($offset + $day) % $cycle !== 0) {
                return false;
            }
        }
        return true;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);
        $sum = 0;
        foreach ($this->snails as $snail) {
            $sum += $this->predictPosition($snail['x'], $snail['y'], 100);
        }

        return $sum;
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);
        $day = 0;
        while (true) {
            if ($this->allAlignedAtDay($day)) {
                return $day - 1;
            }
            $day++;
        }
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);
        $day = 0;
        $step = 1;

        foreach ($this->snails as $snail) {
            $cycle = $this->getCycle($snail['x'], $snail['y']);
            $offset = $this->getOffset($snail['x']);
            $targetRemainder = ($cycle - ($offset % $cycle)) % $cycle;

            while ($day % $cycle !== $targetRemainder) {
                $day += $step;
            }

            $step = $this->lcm($step, $cycle);
        }

        return $day;
    }
}
