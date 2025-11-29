<?php

namespace Events\Y2025;

use Generator;
use SplQueue;

class Quest20
{
    private string $input1 = __DIR__ . '/inputs/Quest20/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest20/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest20/input3.txt';

    private function adj(int $i, int $j): Generator
    {
        yield [$i, $j - 1];
        yield [$i, $j + 1];
        
        if ($j % 2 !== 0) {
            yield [$i + 1, $j - 1];
        } else {
            yield [$i - 1, $j + 1];
        }
    }

    public function solvePart1(): string
    {
        $lines = file($this->input1, FILE_IGNORE_NEW_LINES);
        $grid = array_map(fn($l) => str_replace('.', '', trim($l)), $lines); // Removemos TODOS os pontos, não só das bordas, para ficar denso

        $gridDict = [];
        foreach ($grid as $i => $row) {
            $len = strlen($row);
            for ($j = 0; $j < $len; $j++) {
                $gridDict["$i,$j"] = $row[$j];
            }
        }

        $total = 0;
        foreach ($gridDict as $key => $cell) {
            if ($cell === 'T') {
                [$i, $j] = explode(',', $key);
                foreach ($this->adj((int)$i, (int)$j) as [$ni, $nj]) {
                    if (isset($gridDict["$ni,$nj"]) && $gridDict["$ni,$nj"] === 'T') {
                        $total++;
                    }
                }
            }
        }

        return (string)($total / 2);
    }

    public function solvePart2(): string
    {
        $lines = file($this->input2, FILE_IGNORE_NEW_LINES);
        $grid = array_map(fn($l) => str_replace('.', '', trim($l)), $lines);

        $gridDict = [];
        $start = null;
        $end = null;

        foreach ($grid as $i => $row) {
            $len = strlen($row);
            for ($j = 0; $j < $len; $j++) {
                $cell = $row[$j];
                if ($cell === 'S') {
                    $start = "$i,$j";
                    $cell = 'T';
                } elseif ($cell === 'E') {
                    $end = "$i,$j";
                    $cell = 'T';
                }
                $gridDict["$i,$j"] = $cell;
            }
        }

        return $this->runBFS($start, $end, $gridDict);
    }

    public function solvePart3(): string
    {
        $lines = file($this->input3, FILE_IGNORE_NEW_LINES);
        $grid = array_map(fn($l) => str_replace('.', '', trim($l)), $lines);

        $gridDict = [];
        $start = null;
        $end = null;

        foreach ($grid as $i => $row) {
            $len = strlen($row);
            for ($j = 0; $j < $len; $j++) {
                $cell = $row[$j];
                if ($cell === 'S') {
                    $start = "$i,$j";
                    $cell = 'T';
                } elseif ($cell === 'E') {
                    $end = "$i,$j";
                    $cell = 'T';
                }
                $gridDict["$i,$j"] = $cell;
            }
        }

        $rotationMap = $this->buildRotationMap($grid);

        return $this->runBFS($start, $end, $gridDict, $rotationMap);
    }

    private function runBFS(string $start, string $end, array $gridDict, ?array $rotationMap = null): string
    {
        $costs = [];
        foreach ($gridDict as $k => $v) {
            if ($v === 'T') $costs[$k] = PHP_INT_MAX;
        }
        $costs[$start] = 0;

        $queue = new SplQueue();
        $queue->enqueue($start);
        
        $inQueue = [$start => true];

        while (!$queue->isEmpty()) {
            $p = $queue->dequeue();
            unset($inQueue[$p]);

            $currentCost = $costs[$p];
            $newCost = $currentCost + 1;

            [$i, $j] = explode(',', $p);
            $i = (int)$i; 
            $j = (int)$j;

            $neighbors = [];
            if ($rotationMap) {
                if (isset($rotationMap[$p])) {
                    $neighbors[] = $rotationMap[$p];
                }

                foreach ($this->adj($i, $j) as [$ni, $nj]) {
                    $key = "$ni,$nj";
                    if (isset($rotationMap[$key])) {
                        $neighbors[] = $rotationMap[$key];
                    }
                }
            } else {
                foreach ($this->adj($i, $j) as [$ni, $nj]) {
                    $neighbors[] = "$ni,$nj";
                }
            }

            foreach ($neighbors as $neighborKey) {
                if (isset($costs[$neighborKey]) && $newCost < $costs[$neighborKey]) {
                    $costs[$neighborKey] = $newCost;
                    
                    if (!isset($inQueue[$neighborKey])) {
                        $queue->enqueue($neighborKey);
                        $inQueue[$neighborKey] = true;
                    }
                }
            }
        }

        return (string)($costs[$end] ?? "No path");
    }

    private function buildRotationMap(array $grid): array
    {
        $tempGrid = [];
        foreach ($grid as $i => $row) {
            $len = strlen($row);
            $coordRow = [];
            for ($j = 0; $j < $len; $j++) {
                $coordRow[] = "$i,$j";
            }
            $tempGrid[] = $coordRow;
        }

        $splitGrid = [];
        foreach ($tempGrid as $row) {
            $evens = [];
            $odds = [];
            foreach ($row as $k => $val) {
                if ($k % 2 === 0) $evens[] = $val;
                else $odds[] = $val;
            }
            $splitGrid[] = $evens;
            $splitGrid[] = $odds;
        }

        $splitGrid = array_reverse($splitGrid);

        $maxLen = 0;
        foreach ($splitGrid as $row) {
            $maxLen = max($maxLen, count($row));
        }

        $transposedFlat = [];
        for ($col = 0; $col < $maxLen; $col++) {
            foreach ($splitGrid as $row) {
                if (isset($row[$col])) {
                    $transposedFlat[] = $row[$col];
                }
            }
        }

        $rotationMap = [];
        $flatIndex = 0;
        
        foreach ($grid as $i => $row) {
            $len = strlen($row);
            for ($j = 0; $j < $len; $j++) {
                if (isset($transposedFlat[$flatIndex])) {
                    $origin = $transposedFlat[$flatIndex];
                    $rotationMap["$i,$j"] = $origin;
                }
                $flatIndex++;
            }
        }

        return $rotationMap;
    }
}