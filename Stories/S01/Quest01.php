<?php

namespace Stories\S01;

class Quest01
{
    private string $input1 = __DIR__ . '/../inputs/S01/Quest01/input1.txt';
    private string $input2 = __DIR__ . '/../inputs/S01/Quest01/input2.txt';
    private string $input3 = __DIR__ . '/../inputs/S01/Quest01/input3.txt';

    private array $sets = [];

    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $this->sets = [];

        foreach (file($filePath, FILE_IGNORE_NEW_LINES) as $line) {
            preg_match_all('/\d+/', $line, $m);
            [$A, $B, $C, $X, $Y, $Z, $M] = array_map('intval', $m[0]);

            $this->sets[] = compact('A','B','C','X','Y','Z','M');
        }
    }


    private function computeSequence(int $N, int $mod): array
    {
        $seen = [];
        $seq = [];
        $start = 1;

        while (true) {
            $r = ($start * $N) % $mod;
            if (isset($seen[$r])) {
                return [
                    array_slice($seq, 0, $seen[$r]), // prefix
                    array_slice($seq, $seen[$r])     // cycle
                ];
            }
            $seen[$r] = count($seq);
            $seq[] = $r;
            $start = $r;
        }
    }


    private function eniPart1(int $N, int $exp, int $mod): int
    {
        $start = 1;
        $out = [];

        for ($i = 0; $i < $exp; $i++) {
            $start = ($start * $N) % $mod;
            $out[$exp - $i] = $start;
        }

        ksort($out);
        return (int) implode($out);
    }

    private function eniPart2(int $N, int $exp, int $mod): int
    {
        [$pre, $cycle] = $this->computeSequence($N, $mod);
        $result = [];

        for ($i = max(0, $exp - 5); $i < $exp; $i++) {
            $result[] = $i < count($pre)
                ? $pre[$i]
                : $cycle[($i - count($pre)) % count($cycle)];
        }

        krsort($result);
        return (int) implode('', $result);
    }

    private function eniPart3(int $N, int $exp, int $mod): int
    {
        [$pre, $cycle] = $this->computeSequence($N, $mod);
        $lp = count($pre);
        $lc = count($cycle);

        if ($exp <= $lp) {
            return array_sum(array_slice($pre, 0, $exp));
        }

        $remain = $exp - $lp;
        return array_sum($pre)
            + intdiv($remain, $lc) * array_sum($cycle)
            + array_sum(array_slice($cycle, 0, $remain % $lc));
    }


    private function solve(string $file, callable $fn): int
    {
        $this->parse($file);
        $max = PHP_INT_MIN;

        foreach ($this->sets as $s) {
            $total =
                $fn($s['A'], $s['X'], $s['M']) +
                $fn($s['B'], $s['Y'], $s['M']) +
                $fn($s['C'], $s['Z'], $s['M']);

            $max = max($max, $total);
        }

        return $max;
    }


    public function solvePart1() {
        return $this->solve($this->input1, fn($N,$E,$M)=>$this->eniPart1($N,$E,$M));
    }


    public function solvePart2() {
        return $this->solve($this->input2, fn($N,$E,$M)=>$this->eniPart2($N,$E,$M));
    }


    public function solvePart3() {
        return $this->solve($this->input3, fn($N,$E,$M)=>$this->eniPart3($N,$E,$M));
    }
}