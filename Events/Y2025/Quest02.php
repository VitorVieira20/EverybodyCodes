<?php

namespace Events\Y2025;

class Quest02
{
    private string $input1 = __DIR__ . '/inputs/Quest02/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest02/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest02/input3.txt';

    private int $A1 = 0;
    private int $A2 = 0;
    private int $R1 = 0;
    private int $R2 = 0;


    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        preg_match('/A=\[(-?\d+),(-?\d+)\]/', $lines[0], $matches);

        $this->A1 = (int) $matches[1];
        $this->A2 = (int) $matches[2];
    }


    private function addComplex(int $X1, int $Y1, int $X2, int $Y2)
    {
        return [$X1 + $X2, $Y1 + $Y2];
    }


    private function mulComplex(int $X1, int $Y1, int $X2, int $Y2)
    {
        return [$X1 * $X2 - $Y1 * $Y2, $X1 * $Y2 + $Y1 * $X2];
    }


    private function divComplex(int $X1, int $Y1, int $X2, int $Y2)
    {
        return [
            $X2 !== 0 ? intdiv($X1, $X2) : 0,
            $Y2 !== 0 ? intdiv($Y1, $Y2) : 0
        ];
    }


    private function performCycle()
    {
        [$this->R1, $this->R2] = $this->mulComplex($this->R1, $this->R2, $this->R1, $this->R2);
        [$this->R1, $this->R2] = $this->divComplex($this->R1, $this->R2, 10, 10);
        [$this->R1, $this->R2] = $this->addComplex($this->R1, $this->R2, $this->A1, $this->A2);
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $this->R1 = 0;
        $this->R2 = 0;

        for ($i = 0; $i < 3; $i++) {
            $this->performCycle();
        }

        return "[{$this->R1},{$this->R2}]";
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $X0 = $this->A1;
        $Y0 = $this->A2;
        $X1 = $X0 + 1000;
        $Y1 = $Y0 + 1000;

        $dx = ($X1 - $X0) / 100;
        $dy = ($Y1 - $Y0) / 100;

        $engravedPoints = [];

        for ($i = 0; $i <= 100; $i++) {
            $Px = $X0 + round($i * $dx);
            for ($j = 0; $j <= 100; $j++) {
                $Py = $Y0 + round($j * $dy);

                $R = [0, 0];
                $engrave = true;

                for ($cycle = 0; $cycle < 100; $cycle++) {
                    $R = $this->mulComplex($R[0], $R[1], $R[0], $R[1]);
                    $R = $this->divComplex($R[0], $R[1], 100000, 100000);
                    $R = $this->addComplex($R[0], $R[1], $Px, $Py);

                    if ($R[0] > 1000000 || $R[0] < -1000000 || $R[1] > 1000000 || $R[1] < -1000000) {
                        $engrave = false;
                        break;
                    }
                }

                if ($engrave) {
                    $engravedPoints[] = [$Px, $Py];
                }
            }
        }

        return count($engravedPoints);
    }


    public function solvePart3(): int
    {
        $this->parse($this->input3);

        $gridSize = 101;
        $startX = $this->A1;
        $startY = $this->A2;
        $endX = $this->A1 + 100;
        $endY = $this->A2 + 100;

        $stepX = ($endX - $startX) / ($gridSize - 1);
        $stepY = ($endY - $startY) / ($gridSize - 1);

        $totalEngraved = 0;

        $pointsX = [];
        $pointsY = [];
        for ($i = 0; $i < $gridSize; $i++) {
            $pointsX[$i] = (int) ($startX + $i * $stepX);
            $pointsY[$i] = (int) ($startY + $i * $stepY);
        }

        for ($i = 0; $i < $gridSize; $i++) {
            $px = $pointsX[$i];
            for ($j = 0; $j < $gridSize; $j++) {
                $py = $pointsY[$j];

                $r1 = 0;
                $r2 = 0;
                $engrave = true;

                for ($cycle = 0; $cycle < 100; $cycle++) {
                    $tmp1 = $r1 * $r1 - $r2 * $r2;
                    $tmp2 = 2 * $r1 * $r2;

                    $r1 = intdiv($tmp1, 100000);
                    $r2 = intdiv($tmp2, 100000);

                    $r1 += $px;
                    $r2 += $py;

                    if ($r1 > 1000000 || $r1 < -1000000 || $r2 > 1000000 || $r2 < -1000000) {
                        $engrave = false;
                        break;
                    }
                }

                if ($engrave)
                    $totalEngraved++;
            }
        }

        return $totalEngraved;
    }
}