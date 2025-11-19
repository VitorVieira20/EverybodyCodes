<?php

namespace Events\Y2025;

use Exception;

class Quest12
{
    private string $input1 = __DIR__ . '/inputs/Quest12/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest12/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest12/input3.txt';

    private array $grid = [];
    private int $rows = 0;
    private int $cols = 0;

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->grid = [];
        $this->rows = count($lines);
        $this->cols = strlen($lines[0]);

        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $this->grid[$i][$j] = intval($lines[$i][$j]);
            }
        }
    }


    private function chainReaction(array $barrels)
    {
        $visited = array_fill(0, $this->rows, array_fill(0, $this->cols, false));

        $queue = [];
        $count = 0;

        foreach ($barrels as [$x, $y]) {
            $queue[] = [$x, $y];
            $visited[$x][$y] = true;
            $count++;
        }

        while (!empty($queue)) {
            [$x, $y] = array_shift($queue);
            $currentVal = $this->grid[$x][$y];

            $dirs = [
                [-1, 0],
                [1, 0],
                [0, -1],
                [0, 1],
            ];

            foreach ($dirs as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;

                if ($nx < 0 || $ny < 0 || $nx >= $this->rows || $ny >= $this->cols) {
                    continue;
                }

                if ($visited[$nx][$ny]) {
                    continue;
                }

                $neighbor = $this->grid[$nx][$ny];

                if ($neighbor <= $currentVal) {
                    $visited[$nx][$ny] = true;
                    $queue[] = [$nx, $ny];
                    $count++;
                }
            }
        }

        return $count;
    }


    private function cloneGrid()
    {
        return array_map(fn($row) => $row, $this->grid);
    }


    private function chainReactionSingle(array $grid, int $sx, int $sy)
    {
        $visited = array_fill(0, $this->rows, array_fill(0, $this->cols, false));
        $queue = [[$sx, $sy]];
        $visited[$sx][$sy] = true;
        $count = 1;

        while (!empty($queue)) {
            [$x, $y] = array_shift($queue);
            $currentVal = $grid[$x][$y];

            $dirs = [[-1, 0], [1, 0], [0, -1], [0, 1]];

            foreach ($dirs as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;

                if ($nx < 0 || $ny < 0 || $nx >= $this->rows || $ny >= $this->cols)
                    continue;
                if ($visited[$nx][$ny])
                    continue;
                if ($grid[$nx][$ny] < 0)
                    continue; // apagado

                if ($grid[$nx][$ny] <= $currentVal) {
                    $visited[$nx][$ny] = true;
                    $queue[] = [$nx, $ny];
                    $count++;
                }
            }
        }

        return $count;
    }


    private function findBestStrike(array $grid)
    {
        $bestCount = 0;
        $bestPos = null;

        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {

                if ($grid[$i][$j] < 0)
                    continue;

                $count = $this->chainReactionSingle($grid, $i, $j);

                if ($count > $bestCount) {
                    $bestCount = $count;
                    $bestPos = [$i, $j];
                }
            }
        }

        return [$bestPos, $bestCount];
    }


    private function applyChain(array &$grid, int $sx, int $sy)
    {
        $visited = array_fill(0, $this->rows, array_fill(0, $this->cols, false));
        $queue = [[$sx, $sy]];
        $visited[$sx][$sy] = true;
        $grid[$sx][$sy] = -1;

        while (!empty($queue)) {
            [$x, $y] = array_shift($queue);
            $currentVal = $this->grid[$x][$y];

            $dirs = [[-1, 0], [1, 0], [0, -1], [0, 1]];

            foreach ($dirs as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;

                if ($nx < 0 || $ny < 0 || $nx >= $this->rows || $ny >= $this->cols)
                    continue;
                if ($visited[$nx][$ny])
                    continue;
                if ($grid[$nx][$ny] < 0)
                    continue;

                if ($grid[$nx][$ny] <= $currentVal) {
                    $visited[$nx][$ny] = true;
                    $queue[] = [$nx, $ny];
                    $grid[$nx][$ny] = -1;
                }
            }
        }
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        return $this->chainReaction([[0, 0]]);
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        return $this->chainReaction([[0, 0], [$this->rows - 1, $this->cols - 1]]);
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $workGrid = $this->cloneGrid();

        $strikes = [];

        for ($k = 0; $k < 3; $k++) {
            [$bestPos, $bestCount] = $this->findBestStrike($workGrid);
            $strikes[] = $bestPos;

            $this->applyChain($workGrid, $bestPos[0], $bestPos[1]);
        }

        return $this->chainReaction($strikes);
    }
}
