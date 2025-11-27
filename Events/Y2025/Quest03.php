<?php

namespace Events\Y2025;

class Quest03
{
    private string $input1 = __DIR__ . '/inputs/Quest03/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest03/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest03/input3.txt';

    private array $numbers = [];

    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        preg_match_all('/\d+(?:\.\d+)?/', $lines[0], $matches);

        $this->numbers = array_map(fn($n) => floor(floatval($n)), $matches[0]);
    }


    private function orderNumbers()
    {
        rsort($this->numbers);

        $this->numbers = array_unique($this->numbers);
    }


    public function solvePart1(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input1);

        $this->orderNumbers();

        return array_sum($this->numbers);
    }


    public function solvePart2(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input2);

        $this->orderNumbers();

        return array_sum(array_slice($this->numbers, -20));
    }


    public function solvePart3(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input3);

        rsort($this->numbers);

        $sets = [];

        foreach ($this->numbers as $num) {
            $placed = false;

            for ($i = 0; $i < count($sets); $i++) {
                if ($num < end($sets[$i])) {
                    $sets[$i][] = $num;
                    $placed = true;
                    break;
                }
            }

            if (!$placed) {
                $sets[] = [$num];
            }
        }

        return strval(count($sets));
    }

}