<?php

namespace Events\Y2025;

class Quest04
{
    private string $input1 = __DIR__ . '/inputs/Quest04/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest04/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest04/input3.txt';


    private $gears = [];


    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $this->gears = array_map('trim', $lines);
    }


    private function calcTurns(int $times, bool $isPart1)
    {
        $first = $this->gears[0];
        $last = $this->gears[count($this->gears) - 1];

        if ($isPart1) return (int) ($times * ($first / $last));

        return (int) ceil(($times * ($last / $first)));
    }


    public function solvePart1(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input1);

        return $this->calcTurns(2025, true);
    }


    public function solvePart2(?string $inputFile = null): string
    {
        $this->parse(filePath: $inputFile ?? $this->input2);

        return $this->calcTurns(10000000000000, false);
    }


    public function solvePart3(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input3);

        $gears = [];
        foreach ($this->gears as $line) {
            if ($line === '')
                continue;
            if (strpos($line, '|') !== false) {
                [$l, $r] = array_map('intval', explode('|', $line));
                $gears[] = ['L' => $l, 'R' => $r];
            } else {
                $gears[] = intval($line);
            }
        }

        if (count($gears) === 0)
            return '0';

        $rotations = 100.0;

        $prevTeeth = is_array($gears[0]) ? $gears[0]['R'] : $gears[0];

        for ($i = 1; $i < count($gears); $i++) {
            $current = $gears[$i];

            if (is_array($current)) {
                $leftTeeth = $current['L'];
                if ($leftTeeth == 0) {
                    $rotations = 0.0;
                } else {
                    $rotations *= ($prevTeeth / $leftTeeth);
                }
                $prevTeeth = $current['R'];
            } else {
                if ($current == 0) {
                    $rotations = 0.0;
                } else {
                    $rotations *= ($prevTeeth / $current);
                }
                $prevTeeth = $current;
            }
        }

        $result = (int) floor($rotations);

        return (string) $result;
    }

}