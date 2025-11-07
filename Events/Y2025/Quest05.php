<?php

namespace Events\Y2025;

class Quest05
{
    private string $input1 = __DIR__ . '/inputs/Quest05/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest05/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest05/input3.txt';

    private array $swords = [];


    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $this->swords = [];

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $line) {
            [$id, $nums] = explode(':', $line);
            $numbers = array_map('intval', explode(',', $nums));
            $levels = $this->buildFishboneLevels($numbers);
            $this->swords[] = [
                'id' => (int) $id,
                'numbers' => $numbers,
                'levels' => $levels,
                'quality' => (int) implode('', array_map(fn($lvl) => substr($lvl, 0, strlen($lvl)), $levels))
            ];
        }
    }


    private function buildFishbone($numbers)
    {
        $spine = [$numbers[0]];
        $left = [];
        $right = [];

        foreach (array_slice($numbers, 1) as $num) {
            $placed = false;

            for ($i = 0; $i < count($spine); $i++) {
                $val = $spine[$i];

                if ($num < $val && (!isset($left[$i]))) {
                    $left[$i] = $num;
                    $placed = true;
                    break;
                }

                if ($num > $val && (!isset($right[$i]))) {
                    $right[$i] = $num;
                    $placed = true;
                    break;
                }
            }

            if (!$placed) {
                $spine[] = $num;
            }
        }

        return (int) implode('', $spine);
    }


    private function buildFishboneLevels($numbers)
    {
        $spine = [$numbers[0]];
        $left = [];
        $right = [];

        foreach (array_slice($numbers, 1) as $num) {
            $placed = false;

            for ($i = 0; $i < count($spine); $i++) {
                $val = $spine[$i];

                if ($num < $val && (!isset($left[$i]))) {
                    $left[$i] = $num;
                    $placed = true;
                    break;
                }

                if ($num > $val && (!isset($right[$i]))) {
                    $right[$i] = $num;
                    $placed = true;
                    break;
                }
            }

            if (!$placed) {
                $spine[] = $num;
            }
        }

        $levels = [];
        for ($i = 0; $i < count($spine); $i++) {
            $parts = [];
            if (isset($left[$i]))
                $parts[] = $left[$i];
            $parts[] = $spine[$i];
            if (isset($right[$i]))
                $parts[] = $right[$i];
            $levels[] = (int) implode('', $parts);
        }

        return $levels;
    }


    private function buildFishboneStructure(array $numbers): array
    {
        $spine = [$numbers[0]];
        $left = [];
        $right = [];

        foreach (array_slice($numbers, 1) as $num) {
            $placed = false;
            for ($i = 0; $i < count($spine); $i++) {
                $val = $spine[$i];
                if ($num < $val && !isset($left[$i])) {
                    $left[$i] = $num;
                    $placed = true;
                    break;
                }
                if ($num > $val && !isset($right[$i])) {
                    $right[$i] = $num;
                    $placed = true;
                    break;
                }
            }
            if (!$placed) {
                $spine[] = $num;
            }
        }

        $levels = [];
        for ($i = 0; $i < count($spine); $i++) {
            $parts = [];
            if (isset($left[$i]))
                $parts[] = (string) $left[$i];
            $parts[] = (string) $spine[$i];
            if (isset($right[$i]))
                $parts[] = (string) $right[$i];
            $levels[] = implode('', $parts);
        }

        $spineConcatenation = implode('', array_map('strval', $spine));

        return [
            'spine' => $spine,
            'spine_str' => $spineConcatenation,
            'levels' => $levels
        ];
    }


    private function buildSpine()
    {
        $spine = [
            ['value' => array_shift($this->swords[0]['numbers']), 'left' => null, 'right' => null]
        ];

        foreach ($this->swords[0]['numbers'] as $n) {
            $placed = false;

            for ($i = 0; $i < count($spine); $i++) {
                if ($n < $spine[$i]['value'] && $spine[$i]['left'] === null) {
                    $spine[$i]['left'] = $n;
                    $placed = true;
                    break;
                } elseif ($n > $spine[$i]['value'] && $spine[$i]['right'] === null) {
                    $spine[$i]['right'] = $n;
                    $placed = true;
                    break;
                }
            }

            if (!$placed) {
                $spine[] = ['value' => $n, 'left' => null, 'right' => null];
            }
        }

        return $spine;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $spine = $this->buildSpine();

        $quality = '';
        foreach ($spine as $segment) {
            $quality .= $segment['value'];
        }

        return $quality;
    }


    public function solvePart2(): int
    {
        $this->parse($this->input2);

        $qualities = [];

        foreach ($this->swords as $sword) {
            $quality = $this->buildFishbone($sword['numbers']);
            $qualities[$sword['id']] = $quality;
        }

        return max($qualities) - min($qualities);
    }


    public function solvePart3(): string
    {
        $lines = file($this->input3, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '')
                continue;
            [$id, $nums] = explode(':', $line);
            $id = (int) $id;
            $numbers = array_map('intval', explode(',', $nums));

            $struct = $this->buildFishboneStructure($numbers);

            $swords[] = [
                'id' => $id,
                'spine_str' => $struct['spine_str'],
                'levels' => $struct['levels'],
                'spine_len' => strlen($struct['spine_str'])
            ];
        }

        usort($swords, function ($a, $b) {
            if ($a['spine_len'] !== $b['spine_len']) {
                return $b['spine_len'] <=> $a['spine_len'];
            }
            if ($a['spine_str'] !== $b['spine_str']) {
                return strcmp($b['spine_str'], $a['spine_str']);
            }

            $maxLevels = max(count($a['levels']), count($b['levels']));
            for ($i = 0; $i < $maxLevels; $i++) {
                $AL = isset($a['levels'][$i]) ? (int) $a['levels'][$i] : 0;
                $BL = isset($b['levels'][$i]) ? (int) $b['levels'][$i] : 0;
                if ($AL !== $BL) {
                    return $BL <=> $AL;
                }
            }

            return $b['id'] <=> $a['id'];
        });

        $checksum = 0;
        foreach ($swords as $pos => $sword) {
            $checksum += ($pos + 1) * $sword['id'];
        }

        return (string) $checksum;
    }
}