<?php

namespace Events\Y2025;

class Quest19
{
    private string $input1 = __DIR__ . '/inputs/Quest19/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest19/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest19/input3.txt';

    private array $walls = [];
    private int $maxX = 0;

    private function parse(string $filePath): void
    {
        $this->walls = [];
        $this->maxX = 0;
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (preg_match('/^(\d+),(\d+),(\d+)$/', trim($line), $matches)) {
                $x = (int)$matches[1];
                $yStart = (int)$matches[2];
                $height = (int)$matches[3];
                
                if (!isset($this->walls[$x])) {
                    $this->walls[$x] = [];
                }

                $this->walls[$x][] = [
                    'min' => $yStart,
                    'max' => $yStart + $height - 1
                ];
                
                if ($x > $this->maxX) {
                    $this->maxX = $x;
                }
            }
        }
    }


    private function solve(string $inputFile): string
    {
        $this->parse($inputFile);
        
        $currentStates = [0 => 0];

        for ($x = 1; $x <= $this->maxX; $x++) {
            $nextStates = [];

            foreach ($currentStates as $y => $cost) {
                $flapY = $y + 1;
                $flapCost = $cost + 1;
                if (!isset($nextStates[$flapY]) || $flapCost < $nextStates[$flapY]) {
                    $nextStates[$flapY] = $flapCost;
                }

                $glideY = $y - 1;
                $glideCost = $cost;
                if (!isset($nextStates[$glideY]) || $glideCost < $nextStates[$glideY]) {
                    $nextStates[$glideY] = $glideCost;
                }
            }

            if (isset($this->walls[$x])) {
                foreach ($nextStates as $y => $cost) {
                    $isValid = false;
                    
                    foreach ($this->walls[$x] as $opening) {
                        if ($y >= $opening['min'] && $y <= $opening['max']) {
                            $isValid = true;
                            break;
                        }
                    }

                    if (!$isValid) {
                        unset($nextStates[$y]);
                    }
                }
            }
            
            $currentStates = $nextStates;
        }

        return (string)min($currentStates);
    }


    private function solveOptimized(string $inputFile): string
    {
        $this->parse($inputFile);
        
        $currentStates = [0 => 0];
        $currentX = 0;

        foreach ($this->walls as $nextX => $openings) {
            $dist = $nextX - $currentX;
            $nextStates = [];

            $targetHeights = [];
            foreach ($openings as $op) {
                for ($h = $op['min']; $h <= $op['max']; $h++) {
                    $targetHeights[$h] = true;
                }
            }

            $validTargets = array_keys($targetHeights);

            foreach ($validTargets as $targetY) {
                $bestCostForTarget = PHP_INT_MAX;
                $foundPath = false;

                foreach ($currentStates as $srcY => $srcCost) {
                    if (abs($targetY - $srcY) > $dist) {
                        continue;
                    }

                    $dy = $targetY - $srcY;
                    if (($dist + $dy) % 2 !== 0) {
                        continue;
                    }

                    $flaps = ($dist + $dy) / 2;
                    $totalCost = $srcCost + $flaps;

                    if ($totalCost < $bestCostForTarget) {
                        $bestCostForTarget = $totalCost;
                        $foundPath = true;
                    }
                }

                if ($foundPath) {
                    $nextStates[$targetY] = $bestCostForTarget;
                }
            }

            $currentStates = $nextStates;
            $currentX = $nextX;
        }

        return (string)min($currentStates);
    }


    public function solvePart1(): string
    {
        return $this->solve($this->input1);
    }


    public function solvePart2(): string
    {
        return $this->solve($this->input2);
    }
    
    
    public function solvePart3(): string
    {
        return $this->solveOptimized($this->input3);
    }
}