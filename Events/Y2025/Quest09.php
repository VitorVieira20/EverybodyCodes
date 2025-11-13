<?php

namespace Events\Y2025;

class Quest09
{
    private string $input1 = __DIR__ . '/inputs/Quest09/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest09/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest09/input3.txt';

    private array $dnas = [];

    private function parse(string $filePath): void
    {
        $this->dnas = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $line) {
            $this->dnas[] = explode(':', $line)[1];
        }
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $d1 = $this->dnas[0];
        $d2 = $this->dnas[1];
        $d3 = $this->dnas[2];

        $dnas = [$d1, $d2, $d3];
        $childIndex = -1;

        for ($c = 0; $c < 3; $c++) {
            $child = $dnas[$c];
            $p1 = $dnas[($c + 1) % 3];
            $p2 = $dnas[($c + 2) % 3];

            $valid = true;

            for ($i = 0; $i < strlen($child); $i++) {
                $ch = $child[$i];
                if ($ch !== $p1[$i] && $ch !== $p2[$i]) {
                    $valid = false;
                    break;
                }
            }

            if ($valid) {
                $childIndex = $c;
                break;
            }
        }

        $child = $dnas[$childIndex];
        $p1 = $dnas[($childIndex + 1) % 3];
        $p2 = $dnas[($childIndex + 2) % 3];

        $match1 = 0;
        $match2 = 0;

        for ($i = 0; $i < strlen($child); $i++) {
            if ($child[$i] === $p1[$i])
                $match1++;
            if ($child[$i] === $p2[$i])
                $match2++;
        }

        $similarity = $match1 * $match2;

        return (string) $similarity;
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $dnas = $this->dnas;
        $count = count($dnas);

        $usedChild = array_fill(0, $count, false);
        $totalSimilarity = 0;

        for ($p1 = 0; $p1 < $count; $p1++) {
            for ($p2 = $p1 + 1; $p2 < $count; $p2++) {

                for ($c = 0; $c < $count; $c++) {

                    if ($c === $p1 || $c === $p2)
                        continue;
                    if ($usedChild[$c])
                        continue;

                    $child = $dnas[$c];
                    $parent1 = $dnas[$p1];
                    $parent2 = $dnas[$p2];

                    if (strlen($child) !== strlen($parent1) || strlen($child) !== strlen($parent2)) {
                        continue;
                    }

                    $length = strlen($child);
                    $valid = true;

                    for ($i = 0; $i < $length; $i++) {
                        $ch = $child[$i];
                        if ($ch !== $parent1[$i] && $ch !== $parent2[$i]) {
                            $valid = false;
                            break;
                        }
                    }

                    if (!$valid)
                        continue;

                    $match1 = 0;
                    $match2 = 0;

                    for ($i = 0; $i < $length; $i++) {
                        if ($child[$i] === $parent1[$i])
                            $match1++;
                        if ($child[$i] === $parent2[$i])
                            $match2++;
                    }

                    $similarity = $match1 * $match2;
                    $totalSimilarity += $similarity;

                    $usedChild[$c] = true;
                }
            }
        }


        return (string) $totalSimilarity;
    }



    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $dnas = $this->dnas;
        $count = count($dnas);
        $length = strlen($dnas[0]);

        $uf = new UnionFind($count);

        for ($p1 = 0; $p1 < $count; $p1++) {
            for ($p2 = $p1 + 1; $p2 < $count; $p2++) {

                for ($c = 0; $c < $count; $c++) {
                    if ($c === $p1 || $c === $p2)
                        continue;

                    $child = $dnas[$c];
                    $parent1 = $dnas[$p1];
                    $parent2 = $dnas[$p2];

                    $valid = true;
                    for ($i = 0; $i < $length; $i++) {
                        $ch = $child[$i];
                        if ($ch !== $parent1[$i] && $ch !== $parent2[$i]) {
                            $valid = false;
                            break;
                        }
                    }

                    if ($valid) {
                        $uf->union($p1, $p2);
                        $uf->union($p1, $c);
                        $uf->union($p2, $c);
                    }
                }
            }
        }

        $families = [];

        for ($i = 0; $i < $count; $i++) {
            $root = $uf->find($i);
            $families[$root][] = $i + 1;
        }

        $largest = [];
        foreach ($families as $family) {
            if (count($family) > count($largest)) {
                $largest = $family;
            }
        }

        $sum = array_sum($largest);

        return (string) $sum;
    }
}


class UnionFind
{
    private array $parent;
    private array $rank;

    public function __construct(int $n)
    {
        $this->parent = range(0, $n - 1);
        $this->rank = array_fill(0, $n, 0);
    }

    public function find(int $x): int
    {
        if ($this->parent[$x] !== $x) {
            $this->parent[$x] = $this->find($this->parent[$x]);
        }
        return $this->parent[$x];
    }

    public function union(int $a, int $b): void
    {
        $ra = $this->find($a);
        $rb = $this->find($b);

        if ($ra === $rb)
            return;

        if ($this->rank[$ra] < $this->rank[$rb]) {
            $this->parent[$ra] = $rb;
        } else if ($this->rank[$ra] > $this->rank[$rb]) {
            $this->parent[$rb] = $ra;
        } else {
            $this->parent[$rb] = $ra;
            $this->rank[$ra]++;
        }
    }
}