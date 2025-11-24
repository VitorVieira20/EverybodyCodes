<?php

namespace Events\Y2025;

class Quest16
{
    private string $input1 = __DIR__ . '/inputs/Quest16/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest16/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest16/input3.txt';

    private array $blocks;

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->blocks = explode(',', trim($lines[0]));
    }


    private function getSpellFromFragment(array $wallFragment): array
    {
        $length = count($wallFragment);
        $simulatedWall = array_fill(0, $length, 0);
        $spellNumbers = [];

        for ($i = 0; $i < $length; $i++) {
            $position = $i + 1;
            $realHeight = (int) $wallFragment[$i];
            $currentSimulatedHeight = $simulatedWall[$i];

            if ($realHeight > $currentSimulatedHeight) {
                $spellNumbers[] = $position;
                $diff = $realHeight - $currentSimulatedHeight;

                for ($j = $i; $j < $length; $j += $position) {
                    $simulatedWall[$j] += $diff;
                }
            }
        }
        return $spellNumbers;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $wall = array_fill(0, 90, 0);

        foreach ($this->blocks as $block) {
            $i = $block - 1;
            while ($i < 90) {
                $wall[$i] += 1;
                $i += $block;
            }
        }

        return array_sum($wall);
    }

    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $wallFragment = $this->blocks;

        $spellNumbers = $this->getSpellFromFragment($wallFragment);

        return (string) array_product($spellNumbers);
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $spell = $this->getSpellFromFragment($this->blocks);

        $totalBlocksAvailable = 202520252025000;

        $low = 1;
        $high = $totalBlocksAvailable;
        $ans = 0;

        while ($low <= $high) {
            $mid = (int) floor(($low + $high) / 2);

            $cost = 0;
            foreach ($spell as $s) {
                $cost += floor($mid / $s);
            }

            if ($cost <= $totalBlocksAvailable) {
                $ans = $mid;
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }

        return (string) $ans;
    }
}
