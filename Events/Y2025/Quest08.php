<?php

namespace Events\Y2025;

class Quest08
{
    private string $input1 = __DIR__ . '/inputs/Quest08/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest08/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest08/input3.txt';

    private array $sequence = [];

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->sequence = explode(',', $lines[0]);
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $max = max($this->sequence);

        $count = 0;

        for ($i = 0; $i < count($this->sequence) - 1; $i++) {
            if (abs($this->sequence[$i] - $this->sequence[$i + 1]) === $max / 2) {
                $count++;
            }
        }

        return $count;
    }

    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $seq = array_map('intval', $this->sequence);
        $n = count($seq);
        if ($n < 2)
            return "0";

        $chords = [];

        $countKnots = 0;

        $inArc = function (int $start, int $end, int $x, int $N): bool {
            if ($start == $end)
                return false;
            if ($start < $end) {
                return ($start < $x && $x < $end);
            } else {
                return ($x > $start && $x <= $N) || ($x >= 1 && $x < $end);
            }
        };

        $N = max($seq);

        for ($i = 0; $i < $n - 1; $i++) {
            $a = $seq[$i];
            $b = $seq[$i + 1];

            if ($a == $b) {
                $chords[] = [$a, $b];
                continue;
            }

            foreach ($chords as [$c, $d]) {
                if ($a === $c || $a === $d || $b === $c || $b === $d) {
                    continue;
                }

                $ac = $inArc($a, $b, $c, $N);
                $ad = $inArc($a, $b, $d, $N);
                $ca = $inArc($c, $d, $a, $N);
                $cb = $inArc($c, $d, $b, $N);

                if ((($ac xor $ad) && ($ca xor $cb))) {
                    $countKnots++;
                }
            }

            $chords[] = [$a, $b];
        }

        return (string) $countKnots;
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);
        $seq = array_map('intval', $this->sequence);
        $n = count($seq);
        if ($n < 2)
            return "0";

        $N = max($seq);

        $chords = [];
        for ($i = 0; $i < $n - 1; $i++) {
            $a = $seq[$i];
            $b = $seq[$i + 1];
            if ($a !== $b) {
                $chords[] = [$a, $b];
            }
        }

        $inArc = function (int $start, int $end, int $x, int $N): bool {
            if ($start == $end)
                return false;
            if ($start < $end) {
                return ($start < $x && $x < $end);
            } else {
                return ($x > $start && $x <= $N) || ($x >= 1 && $x < $end);
            }
        };

        $maxCuts = 0;

        for ($i = 1; $i <= $N; $i++) {
            for ($j = $i + 1; $j <= $N; $j++) {
                $cuts = 0;

                foreach ($chords as [$a, $b]) {
                    if ($a === $i || $a === $j || $b === $i || $b === $j)
                        continue;

                    if (
                        ($a == $i && $b == $j) || ($a == $j && $b == $i) ||
                        ((($inArc($i, $j, $a, $N) xor $inArc($i, $j, $b, $N)) &&
                            ($inArc($a, $b, $i, $N) xor $inArc($a, $b, $j, $N))))
                    ) {
                        $cuts++;
                    }

                }

                if ($cuts > $maxCuts) {
                    $maxCuts = $cuts;
                }
            }
        }

        return (string) $maxCuts;
    }
}
