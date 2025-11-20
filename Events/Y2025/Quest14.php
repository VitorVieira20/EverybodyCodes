<?php

namespace Events\Y2025;

class Quest14
{
    private string $input1 = __DIR__ . '/inputs/Quest14/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest14/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest14/input3.txt';

    private array $grid;
    private array $pattern;

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->grid = [];
        foreach ($lines as $line) {
            $this->grid[] = str_split($line);
        }
    }


    private function performRound()
    {
        $newGrid = [];
        $dirs = [
            [-1, -1],
            [-1, 1],
            [1, -1],
            [1, 1]
        ];

        for ($i = 0; $i < count($this->grid); $i++) {
            for ($j = 0; $j < count($this->grid[$i]); $j++) {
                $count = 0;

                foreach ($dirs as [$dx, $dy]) {
                    $ni = $i + $dx;
                    $nj = $j + $dy;

                    if (isset($this->grid[$ni][$nj]) && $this->grid[$ni][$nj] === "#") {
                        $count++;
                    }
                }

                if ($this->grid[$i][$j] === "#") {
                    $newGrid[$i][$j] = ($count % 2 === 1) ? "#" : ".";
                } else {
                    $newGrid[$i][$j] = ($count % 2 === 0) ? "#" : ".";
                }
            }
        }

        $this->grid = $newGrid;
    }


    private function countActive()
    {
        $count = 0;

        for ($i = 0; $i < count($this->grid); $i++) {
            for ($j = 0; $j < count($this->grid[$i]); $j++) {
                if ($this->grid[$i][$j] === '#')
                    $count++;
            }
        }

        return $count;
    }


    private function matched()
    {
        $matches = true;

        $patternHeight = count($this->pattern);
        $patternWidth = count($this->pattern[0]);
        $gridHeight = count($this->grid);
        $gridWidth = count($this->grid[0]);

        $startRow = intval(($gridHeight - $patternHeight) / 2);
        $startCol = intval(($gridWidth - $patternWidth) / 2);
        $matches = true;
        for ($i = 0; $i < $patternHeight; $i++) {
            for ($j = 0; $j < $patternWidth; $j++) {
                if ($this->grid[$startRow + $i][$startCol + $j] !== $this->pattern[$i][$j]) {
                    $matches = false;
                    break 2;
                }
            }
        }

        return $matches;
    }

    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $this->performRound();
            $sum += $this->countActive();
        }

        return $sum;
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $sum = 0;
        for ($i = 0; $i < 2025; $i++) {
            $this->performRound();
            $sum += $this->countActive();
        }

        return $sum;
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $this->pattern = $this->grid;
        $this->grid = array_fill(0, 34, array_fill(0, 34, '.'));

        $totalRounds = 1000000000;
        $hashes = [];
        $matchesPerState = [];
        $rounds = 0;

        while ($rounds < $totalRounds) {
            $this->performRound();

            $gridHash = md5(implode('', array_map(fn($row) => implode('', $row), $this->grid)));

            $matchesPerState[] = $this->matched() ? $this->countActive() : 0;

            if (isset($hashes[$gridHash])) {
                $cycleStart = $hashes[$gridHash];
                $cycleLength = $rounds - $cycleStart;

                $preCycleSum = array_sum(array_slice($matchesPerState, 0, $cycleStart));
                $cycleSum = array_sum(array_slice($matchesPerState, $cycleStart, $cycleLength));

                $remainingRounds = $totalRounds - $cycleStart;
                $numCycles = intdiv($remainingRounds, $cycleLength);
                $rest = $remainingRounds % $cycleLength;

                $restSum = array_sum(array_slice($matchesPerState, $cycleStart, $rest));

                $totalMatches = $preCycleSum + $numCycles * $cycleSum + $restSum;

                return (string) $totalMatches;
            }

            $hashes[$gridHash] = $rounds;
            $rounds++;
        }

        return (string) array_sum($matchesPerState);

    }
}
