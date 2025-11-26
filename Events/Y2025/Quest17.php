<?php

namespace Events\Y2025;

class Quest17
{
    private string $input1 = __DIR__ . '/inputs/Quest17/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest17/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest17/input3.txt';

    private array $grid = [];
    private array $stars = [];
    private int $centerX;
    private int $centerY;
    private int $startX;
    private int $startY;
    private int $maxR;

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $this->grid = [];
        $this->stars = [];
        $this->startX = $this->startY = -1;

        for ($y = 0; $y < count($lines); $y++) {
            $line = $lines[$y];
            $this->grid[$y] = [];

            for ($x = 0; $x < strlen($line); $x++) {
                $char = $line[$x];
                $this->grid[$y][$x] = $char;

                if ($char === '@') {
                    $this->centerX = $x;
                    $this->centerY = $y;
                } elseif ($char === 'S') {
                    $this->startX = $x;
                    $this->startY = $y;
                } elseif (ctype_digit($char)) {
                    $this->stars[] = ['value' => (int)$char, 'x' => $x, 'y' => $y];
                }
            }
        }

        $last = end($this->stars);
        $this->maxR = min(abs($this->centerX - $last['x']), abs($this->centerY - $last['y'])) - 1;
    }

    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $maxRadius = 10;
        $count = 0;

        foreach ($this->stars as $star) {
            $x = $star['x'];
            $y = $star['y'];
            if (($x - $this->centerX) ** 2 + ($y - $this->centerY) ** 2 <= $maxRadius ** 2) {
                $count += $star['value'];
            }
        }

        return (string)$count;
    }

    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $max = PHP_INT_MIN;

        for ($i = 1; $i < $this->maxR; $i++) {
            $sum = 0;
            foreach ($this->stars as $key => $star) {
                $x = $star['x'];
                $y = $star['y'];
                if (($x - $this->centerX) ** 2 + ($y - $this->centerY) ** 2 <= $i ** 2) {
                    unset($this->stars[$key]);
                    $sum += $star['value'];
                }
            }

            $max = max($max, ($i * $sum));
        }

        return (string)$max;
    }

    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $sx = $this->startX;
        $sy = $this->startY;
        $cx = $this->centerX;
        $cy = $this->centerY;

        $minScore = PHP_INT_MAX;
        
        $mapSize = count($this->grid) + count($this->grid[0]); 

        for ($r = 1; $r < $mapSize; $r++) {
            
            $rSq = $r ** 2;

            $distSqS = ($sx - $cx)**2 + ($sy - $cy)**2;
            if ($distSqS <= $rSq) continue;

            $pq = new \SplPriorityQueue();
            $pq->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
            
            $visited = [];
            
            $pq->insert(['x' => $sx, 'y' => $sy, 'p' => 0, 'time' => 0], 0);
            $visited[$sy][$sx][0] = 0;

            $bestTimeForR = PHP_INT_MAX;

            while (!$pq->isEmpty()) {
                $curr = $pq->extract();
                $state = $curr['data'];
                $time = $state['time'];
                $x = $state['x'];
                $y = $state['y'];
                $p = $state['p'];

                if (isset($visited[$y][$x][$p]) && $visited[$y][$x][$p] < $time) continue;
                if ($time >= $bestTimeForR) continue;

                if ($x == $sx && $y == $sy && $time > 0) {
                    if ($p === 1) { 
                        $bestTimeForR = $time;
                        break; 
                    }
                }

                foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as [$dx, $dy]) {
                    $nx = $x + $dx;
                    $ny = $y + $dy;

                    if (!isset($this->grid[$ny][$nx])) continue;
                    
                    $char = $this->grid[$ny][$nx];
                    if ($char === '@') continue;

                    $distSq = ($nx - $cx)**2 + ($ny - $cy)**2;
                    if ($distSq <= $rSq) continue;

                    $cost = 0;
                    if (ctype_digit($char)) {
                        $cost = (int)$char;
                    } 
                    
                    $newTime = $time + $cost;

                    $newP = $p;
                    if ($ny < $cy) {
                        if ($x < $cx && $nx >= $cx) $newP = 1 - $newP;
                        elseif ($x >= $cx && $nx < $cx) $newP = 1 - $newP;
                    }

                    if (!isset($visited[$ny][$nx][$newP]) || $newTime < $visited[$ny][$nx][$newP]) {
                        $visited[$ny][$nx][$newP] = $newTime;
                        $pq->insert(['x' => $nx, 'y' => $ny, 'p' => $newP, 'time' => $newTime], -$newTime);
                    }
                }
            }

            if ($bestTimeForR !== PHP_INT_MAX) {
                $actualLavaRadius = intdiv($bestTimeForR, 30);
                
                if ($actualLavaRadius <= $r) {
                    $score = $bestTimeForR * $actualLavaRadius;
                    if ($score > 0) {
                        $minScore = min($minScore, $score);
                    }
                }
            }
        }

        return (string)$minScore;
    }
}
