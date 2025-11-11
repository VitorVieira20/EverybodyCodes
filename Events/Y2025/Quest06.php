<?php

namespace Events\Y2025;

class Quest06
{
    private string $input1 = __DIR__ . '/inputs/Quest06/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest06/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest06/input3.txt';

    private string $letters = '';

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $this->letters = trim($lines[0]);
    }

    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $filtered = preg_replace('/[^Aa]/', '', $this->letters);
        $mentors = 0;
        $pairs = 0;

        for ($i = 0; $i < strlen($filtered); $i++) {
            if ($filtered[$i] === 'A') $mentors++;
            elseif ($filtered[$i] === 'a') $pairs += $mentors;
        }

        return (string)$pairs;
    }

    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $totalPairs = 0;
        $uniqueCategories = [];

        foreach (str_split($this->letters) as $ch) {
            $uniqueCategories[strtolower($ch)] = true;
        }

        foreach (array_keys($uniqueCategories) as $cat) {
            $upper = strtoupper($cat);
            $lower = strtolower($cat);

            $filtered = preg_replace("/[^{$upper}{$lower}]/", '', $this->letters);

            $mentors = 0;
            $pairs = 0;

            for ($i = 0; $i < strlen($filtered); $i++) {
                if ($filtered[$i] === $upper) $mentors++;
                elseif ($filtered[$i] === $lower) $pairs += $mentors;
            }

            $totalPairs += $pairs;
        }

        return (string)$totalPairs;
    }

    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $segment = $this->letters;
        $L = strlen($segment);
        $REPEATS = 1000;
        $RANGE = 1000;

        $uniqueCategories = [];
        foreach (str_split($segment) as $ch) {
            $uniqueCategories[strtolower($ch)] = true;
        }

        $totalPairs = 0;

        foreach (array_keys($uniqueCategories) as $cat) {
            $upper = strtoupper($cat);
            $lower = strtolower($cat);

            $mentorPositions = [];
            $novicePositions = [];

            for ($i = 0; $i < $L; $i++) {
                if ($segment[$i] === $upper) $mentorPositions[] = $i;
                elseif ($segment[$i] === $lower) $novicePositions[] = $i;
            }

            if (empty($mentorPositions) || empty($novicePositions)) continue;

            $pairs = 0;

            for ($repeat = 0; $repeat < $REPEATS; $repeat++) {
                $offset = $repeat * $L;

                foreach ($novicePositions as $nPos) {
                    $globalN = $offset + $nPos;

                    $minPos = $globalN - $RANGE;
                    $maxPos = $globalN + $RANGE;

                    $startSeg = max(0, intdiv($minPos, $L));
                    $endSeg = min($REPEATS - 1, intdiv($maxPos, $L));

                    for ($seg = $startSeg; $seg <= $endSeg; $seg++) {
                        $segOffset = $seg * $L;
                        foreach ($mentorPositions as $mPos) {
                            $globalM = $segOffset + $mPos;
                            if ($globalM >= $minPos && $globalM <= $maxPos) {
                                $pairs++;
                            }
                        }
                    }
                }
            }

            $totalPairs += $pairs;
        }

        return (string)$totalPairs;
    }
}
