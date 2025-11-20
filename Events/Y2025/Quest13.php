<?php

namespace Events\Y2025;

class Quest13
{
    private string $input1 = __DIR__ . '/inputs/Quest13/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest13/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest13/input3.txt';

    private array $dial;
    private int $center;

    private function parse(string $filePath, bool $isPart1): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->dial = [];

        $this->center = 0;
        $this->dial[$this->center] = 1;

        $i = 1;
        $right = true;

        if ($isPart1) {
            $this->insertOne($lines, $i, $right);
        } else {
            $this->insertRange($lines, $i, $right);
        }

        ksort($this->dial);
    }


    private function parse3(string $filePath)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->dial = [];
        $intervals = [];
        foreach ($lines as $line) {
            [$a, $b] = explode("-", $line);
            $intervals[] = [(int) $a, (int) $b];
        }

        $this->dial = [
            [1, 1]
        ];

        $right = true;

        foreach ($intervals as [$x, $y]) {

            if ($right) {
                $this->dial[] = [$x, $y];
            } else {
                array_unshift($this->dial, [$y, $x]);
            }

            $right = !$right;
        }

        $index_of_one = array_search([1, 1], $this->dial);

        $this->dial = array_merge(
            array_slice($this->dial, $index_of_one),
            array_slice($this->dial, 0, $index_of_one)
        );
    }


    private function insertOne(array $data, int $index, bool $right)
    {
        foreach ($data as $num) {
            if ($right) {
                $this->dial[$this->center + $index] = (int) $num;
            } else {
                $this->dial[$this->center - $index] = (int) $num;
                $index++;
            }
            $right = !$right;
        }
    }


    private function insertRange(array $data, int $index, bool $right)
    {
        foreach ($data as $range) {
            [$start, $end] = explode("-", $range);
            $start = (int) $start;
            $end = (int) $end;

            $rangeSize = $end - $start + 1;

            if ($right) {
                for ($offset = 0; $offset < $rangeSize; $offset++) {
                    $this->dial[$this->center + $index + $offset] = $start + $offset;
                }
            } else {
                for ($offset = 0; $offset < $rangeSize; $offset++) {
                    $this->dial[$this->center - $index - $offset] = $start + $offset;
                }
            }

            $index += $rangeSize;
            $right = !$right;
        }
    }


    private function simulateDial(int $n)
    {
        $times = $n % count($this->dial);

        $keys = array_keys($this->dial);
        $N = count($keys);
        $startIdx = array_search($this->center, $keys);

        $finalIdx = ($startIdx + $times) % $N;

        return $this->dial[$keys[$finalIdx]];
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1, true);

        return $this->simulateDial(2025);
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2, false);

        return $this->simulateDial(20252025);
    }


    public function solvePart3(): string
    {
        $this->parse3($this->input3);

        $size = 0;
        foreach ($this->dial as [$x, $y]) {
            $size += abs($x - $y) + 1;
        }

        $index = 202520252025 % $size;

        foreach ($this->dial as [$x, $y]) {

            if ($index > abs($x - $y)) {
                $index -= abs($x - $y) + 1;
                continue;
            }

            if ($x > $y) {
                return $x - $index;
            } else {
                return $x + $index;
            }
        }

        return "";
    }
}
