<?php

namespace Events\Y2025;

use Exception;

class Quest11
{
    private string $input1 = __DIR__ . '/inputs/Quest11/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest11/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest11/input3.txt';

    private array $ducksCols;
    private int $rounds;


    private function parse(string $filePath): void
    {
        $this->rounds = 0;
        $this->ducksCols = [];

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        for ($i = 0; $i < count($lines); $i++) {
            $this->ducksCols[$i + 1] = (int) $lines[$i];
        }
    }


    private function phase1()
    {
        while (true) {
            $moves = 0;

            for ($i = 1; $i < count($this->ducksCols); $i++) {
                if ($this->ducksCols[$i] > $this->ducksCols[$i + 1]) {
                    $this->ducksCols[$i]--;
                    $this->ducksCols[$i + 1]++;
                    $moves++;
                }
            }

            if ($moves === 0)
                return;

            $this->rounds++;
        }
    }


    private function phase2(bool $isPart1)
    {
        while (true) {
            if ($this->rounds === 10 && $isPart1)
                return;

            $moves = 0;

            for ($i = 1; $i < count($this->ducksCols); $i++) {
                if ($this->ducksCols[$i] < $this->ducksCols[$i + 1]) {
                    $this->ducksCols[$i + 1]--;
                    $this->ducksCols[$i]++;
                    $moves++;
                }
            }

            if ($moves === 0)
                return;

            $this->rounds++;
        }
    }


    private function checksum(): int
    {
        $sum = 0;
        for ($i = 1; $i <= count($this->ducksCols); $i++) {
            $sum += $i * $this->ducksCols[$i];
        }

        return $sum;
    }


    private function predictRounds()
    {
        $average = intdiv(array_sum($this->ducksCols), count($this->ducksCols));

        $sum = 0;
        foreach ($this->ducksCols as $num) {
            if ($num < $average) {
                $sum += ($average - $num);
            }
        }

        return $sum;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $this->phase1();

        $this->phase2(true);

        return $this->checksum();
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $this->phase1();

        $this->phase2(true);

        return $this->rounds;
    }




    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $rounds = $this->predictRounds();

        return $rounds;
    }
}
