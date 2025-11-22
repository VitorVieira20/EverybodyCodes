<?php

namespace Events\Y2025;

class Quest15
{
    private string $input1 = __DIR__ . '/inputs/Quest15/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest15/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest15/input3.txt';

    private array $instructions;

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $this->instructions = explode(',', trim($lines[0]));
    }


    private function performSearch()
    {
        $x = 0;
        $y = 0;

        $segments = [];

        $interestingX = [0];
        $interestingY = [0];

        $dirs = [[-1, 0], [0, 1], [1, 0], [0, -1]];
        $dirIndex = 0;

        $startX = 0; $startY = 0;

        foreach ($this->instructions as $instr) {
            $turn = $instr[0];
            $steps = (int)substr($instr, 1);

            if ($turn === 'L') $dirIndex = ($dirIndex + 3) % 4;
            else               $dirIndex = ($dirIndex + 1) % 4;

            [$dx, $dy] = $dirs[$dirIndex];

            $nextX = $x + ($dx * $steps);
            $nextY = $y + ($dy * $steps);

            $segments[] = [$x, $y, $nextX, $nextY];

            $x = $nextX;
            $y = $nextY;

            $interestingX[] = $x; $interestingX[] = $x - 1; $interestingX[] = $x + 1;
            $interestingY[] = $y; $interestingY[] = $y - 1; $interestingY[] = $y + 1;
        }

        $endX = $x; $endY = $y;


        $sortedX = array_unique($interestingX);
        sort($sortedX);
        $sortedY = array_unique($interestingY);
        sort($sortedY);

        $sortedX = array_values($sortedX);
        $sortedY = array_values($sortedY);

        $countX = count($sortedX);
        $countY = count($sortedY);

        $sIdxX = array_search($startX, $sortedX);
        $sIdxY = array_search($startY, $sortedY);
        $eIdxX = array_search($endX, $sortedX);
        $eIdxY = array_search($endY, $sortedY);

        $queue = new \SplPriorityQueue();
        $queue->insert([$sIdxX, $sIdxY, 0], 0);

        $visited = [];

        while (!$queue->isEmpty()) {
            [$idxX, $idxY, $cost] = $queue->extract();

            if ($idxX === $eIdxX && $idxY === $eIdxY) {
                return (string)$cost;
            }

            $key = "$idxX,$idxY";
            if (isset($visited[$key]) && $visited[$key] <= $cost) {
                continue;
            }
            $visited[$key] = $cost;

            $moves = [[0, 1], [0, -1], [1, 0], [-1, 0]];

            foreach ($moves as [$mx, $my]) {
                $nIdxX = $idxX + $mx;
                $nIdxY = $idxY + $my;

                if ($nIdxX < 0 || $nIdxX >= $countX || $nIdxY < 0 || $nIdxY >= $countY) {
                    continue;
                }

                $realX = $sortedX[$nIdxX];
                $realY = $sortedY[$nIdxY];

                $isStartOrEnd = ($realX === $startX && $realY === $startY) || ($realX === $endX && $realY === $endY);

                if (!$isStartOrEnd && $this->isWall($realX, $realY, $segments)) {
                   continue;
                }

                $dist = abs($realX - $sortedX[$idxX]) + abs($realY - $sortedY[$idxY]);

                $newCost = $cost + $dist;
                $queue->insert([$nIdxX, $nIdxY, $newCost], -$newCost);
            }
        }

        return "Caminho não encontrado";
    }


    private function isWall(int $rx, int $ry, array $segments): bool
    {
        foreach ($segments as [$x1, $y1, $x2, $y2]) {
            if (
                $rx >= min($x1, $x2) && $rx <= max($x1, $x2) &&
                $ry >= min($y1, $y2) && $ry <= max($y1, $y2)
            ) {
                return true;
            }
        }
        return false;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        return $this->performSearch();
    }

    public function solvePart2(): string
    {
        $this->parse($this->input2);

        return $this->performSearch();
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);

        return $this->performSearch();
    }
}
