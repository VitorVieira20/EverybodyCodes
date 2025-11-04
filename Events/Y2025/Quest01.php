<?php

namespace Events\Y2025;

class Quest01
{
    private string $input1 = __DIR__ . '/inputs/Quest01/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest01/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest01/input3.txt';

    private array $names = [];
    private array $instructions = [];

    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $this->names = explode(',', $lines[0]);
        $this->instructions = explode(',', $lines[2]);
    }

    public function solvePart1(): string
    {
        $this->parse($this->input1);
        $maxIndex = count($this->names) - 1;
        $current = 0;

        foreach ($this->instructions as $instruction) {
            $amount = (int) substr($instruction, 1);
            $isRight = $instruction[0] === 'R';

            $current = $isRight
                ? min($current + $amount, $maxIndex)
                : max($current - $amount, 0);
        }

        return $this->names[$current];
    }

    public function solvePart2(): string
    {
        $this->parse($this->input2);
        $total = count($this->names);
        $current = 0;

        foreach ($this->instructions as $instruction) {
            $amount = (int) substr($instruction, 1);
            $isRight = $instruction[0] === 'R';

            $current = $isRight
                ? ($current + $amount) % $total
                : ($current - $amount + $total) % $total;
        }

        return $this->names[$current];
    }

    public function solvePart3(): string
    {
        $this->parse($this->input3);
        $total = count($this->names);

        foreach ($this->instructions as $instruction) {
            $amount = (int) substr($instruction, 1);
            $isRight = $instruction[0] === 'R';

            $target = $isRight
                ? $amount % $total
                : (($total - ($amount % $total)) % $total);

            [$this->names[0], $this->names[$target]] = [$this->names[$target], $this->names[0]];
        }

        return $this->names[0];
    }
}