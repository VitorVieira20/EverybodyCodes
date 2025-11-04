<?php

namespace Stories\S02;

class Quest01
{
    private string $input1 = __DIR__ . '/../inputs/S02/Quest01/input1.txt';
    private string $input2 = __DIR__ . '/../inputs/S02/Quest01/input2.txt';
    private string $input3 = __DIR__ . '/../inputs/S02/Quest01/input3.txt';

    private array $map = [];
    private array $sequences = [];


    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $this->map = [];
        $this->sequences = [];

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $readingMap = true;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                $readingMap = false;
                continue;
            }
            $readingMap
                ? $this->map[] = str_split($line)
                : $this->sequences[] = $line;
        }
    }

    private function numSlots(): int
    {
        return (count($this->map[0]) + 1) / 2;
    }


    private function simulateToken(string $sequence, int $startSlotIndex): int
    {
        $moves = str_split($sequence);
        $H = count($this->map);
        $W = count($this->map[0]);

        $x = $startSlotIndex * 2;
        $y = 0;
        $mi = 0;

        while (true) {
            if ($this->map[$y][$x] === '*') {
                $mv = ($mi < count($moves)) ? $moves[$mi++] : 'L';

                if ($x == 0) {
                    $x = 1;
                } else if ($x == $W - 1) {
                    $x = $W - 2;
                } else {
                    $x += ($mv == 'L') ? -1 : 1;
                }
            }

            if ($y == $H - 1)
                break;

            $y += 1;
        }

        $finalSlot = $x / 2 + 1;
        $tossSlot = $startSlotIndex + 1;
        $coins = $finalSlot * 2 - $tossSlot;
        return max($coins, 0);
    }

    public function solvePart1(): int
    {
        $this->parse($this->input1);

        $total = 0;
        for ($i = 0; $i < count($this->sequences); $i++) {
            $score = $this->simulateToken($this->sequences[$i], $i);
            $total += $score;
        }

        return $total;
    }


    public function solvePart2(): int
    {
        $this->parse($this->input2);

        $total = 0;
        $numSlots = $this->numSlots();

        for ($i = 0; $i < count($this->sequences); $i++) {
            $best = 0;
            for ($j = 0; $j < $numSlots; $j++) {
                $best = max($best, $this->simulateToken($this->sequences[$i], $j));
            }
            $total += $best;
        }

        return $total;
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $numTokens = count($this->sequences);
        $numSlots = $this->numSlots();

        $coins = [];
        for ($t = 0; $t < $numTokens; $t++) {
            for ($s = 0; $s < $numSlots; $s++) {
                $coins[$t][$s] = $this->simulateToken($this->sequences[$t], $s);
            }
        }

        $maxCoins = [0];
        $minCoins = [PHP_INT_MAX];
        $usedSlots = array_fill(0, $numSlots, false);

        $this->dfs(0, $numTokens, $numSlots, $coins, $usedSlots, 0, $maxCoins, $minCoins);

        return $minCoins[0] . " " . $maxCoins[0];
    }

    private function dfs(int $tokenIndex, int $numTokens, int $numSlots, array $coins, array $usedSlots, int $sumSoFar, array &$maxCoins, array &$minCoins) {
        if ($tokenIndex == $numTokens) {
            $maxCoins[0] = max($maxCoins[0], $sumSoFar);
            $minCoins[0] = min($minCoins[0], $sumSoFar);
            return;
        }

        for ($s = 0; $s < $numSlots; $s++) {
            if (!$usedSlots[$s]) {
                $usedSlots[$s] = true;
                $this->dfs($tokenIndex + 1, $numTokens, $numSlots, $coins, $usedSlots, $sumSoFar + $coins[$tokenIndex][$s], $maxCoins, $minCoins);
                $usedSlots[$s] = false;
            }
        }
    }
}