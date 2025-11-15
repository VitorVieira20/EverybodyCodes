<?php

namespace Events\Y2025;

class Quest10
{
    private string $input1 = __DIR__ . '/inputs/Quest10/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest10/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest10/input3.txt';

    private array $grid = [];
    private int $sheeps = 0;
    private array $sheepsPositions = [];
    private array $dragonPositions = [];

    private function parse(string $filePath): void
    {
        $this->grid = [];
        $this->sheepsPositions = [];
        $this->dragonPositions = [];
        $this->sheeps = 0;

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        for ($i = 0; $i < count($lines); $i++) {
            for ($j = 0; $j < strlen($lines[$i]); $j++) {
                if ($lines[$i][$j] === 'D')
                    $this->dragonPositions["$i,$j"] = true;
                if ($lines[$i][$j] === 'S')
                    $this->sheepsPositions[] = [$i, $j];
                $this->grid[$i][$j] = $lines[$i][$j];
            }
        }
    }


    private function move($x, $y)
    {
        $moves = [[2, 1], [2, -1], [-2, 1], [-2, -1], [1, 2], [1, -2], [-1, 2], [-1, -2]];

        foreach ($moves as [$dx, $dy]) {
            if (isset($this->grid[$x + $dx][$y + $dy])) {
                if ($this->grid[$x + $dx][$y + $dy] === 'S') {
                    $this->sheeps++;
                    $this->grid[$x + $dx][$y + $dy] = '.';
                }

                $newX = $x + $dx;
                $newY = $y + $dy;
                $this->dragonPositions["$newX,$newY"] = true;
            }
        }
    }


    private function normalizeSheep(array $sheep): array
    {
        sort($sheep);
        return $sheep;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $rounds = 4;

        for ($i = 0; $i < $rounds; $i++) {
            foreach ($this->dragonPositions as $key => $value) {
                [$x, $y] = explode(',', $key);
                $this->move($x, $y);
            }
        }

        return $this->sheeps;
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $ROWS = count($this->grid);
        $COLS = count($this->grid[0]);

        // Encontrar o dragão inicial
        $dragon = null;
        for ($r = 0; $r < $ROWS; $r++) {
            for ($c = 0; $c < $COLS; $c++) {
                if ($this->grid[$r][$c] === 'D') {
                    $dragon = [$r, $c];
                }
            }
        }

        if (!$dragon) {
            return "0";
        }

        $dragonCanMoveTo = function ($drg) use ($ROWS, $COLS) {
            $spots = [];
            $pairA = [[1, 2], [2, 1]];
            $diagonals = [[1, 1], [1, -1], [-1, 1], [-1, -1]];

            foreach ($pairA as [$dr, $dc]) {
                foreach ($diagonals as [$sr, $sc]) {
                    $r = $drg[0] + $sr * $dr;
                    $c = $drg[1] + $sc * $dc;

                    if ($r >= 0 && $r < $ROWS && $c >= 0 && $c < $COLS) {
                        $spots[] = [$r, $c];
                    }
                }
            }
            return $spots;
        };

        $s = [];
        $s[implode(',', $dragon)] = $dragon;

        $e = [];

        $turns = 20;

        for ($turn = 0; $turn < $turns; $turn++) {

            $newS = [];

            foreach ($s as $d) {
                $spots = $dragonCanMoveTo($d);
                foreach ($spots as $sp) {
                    $newS[$sp[0] . "," . $sp[1]] = $sp;
                }
            }

            $s = $newS;

            foreach ($s as [$r, $c]) {

                if ($this->grid[$r][$c] === "#")
                    continue;

                $possible = [$r - $turn, $r - $turn - 1];

                foreach ($possible as $sr) {
                    if ($sr >= 0 && $this->grid[$sr][$c] === "S") {
                        $e[$sr . "," . $c] = true;
                    }
                }
            }
        }

        return strval(count($e));
    }




    public function solvePart3(): string
    {
        $this->parse($this->input3);

        $ROWS = count($this->grid);
        $COLS = count($this->grid[0]);

        $dragon = null;
        $sheep = [];

        for ($r = 0; $r < $ROWS; $r++) {
            for ($c = 0; $c < $COLS; $c++) {
                if ($this->grid[$r][$c] === "D") {
                    $dragon = [$r, $c];
                } elseif ($this->grid[$r][$c] === "S") {
                    $sheep[] = [$r, $c];
                }
            }
        }

        $sheep = $this->normalizeSheep($sheep);

        $dragonCanMoveTo = function (array $dragonPos) use ($ROWS, $COLS): array {
            $spots = [];
            $pairA = [[1, 2], [2, 1]];
            $diagonals = [[1, 1], [1, -1], [-1, 1], [-1, -1]];

            foreach ($pairA as [$dr, $dc]) {
                foreach ($diagonals as [$sr, $sc]) {
                    $r = $dragonPos[0] + $sr * $dr;
                    $c = $dragonPos[1] + $sc * $dc;
                    if ($r >= 0 && $r < $ROWS && $c >= 0 && $c < $COLS) {
                        $spots[] = [$r, $c];
                    }
                }
            }

            return $spots;
        };

        static $memo = [];

        $count = function ($sheep, $dragonPos, $turn) use (&$count, &$memo, $ROWS, $COLS, $dragonCanMoveTo) {

            $key = $turn . "|" . json_encode($sheep) . "|" . $dragonPos[0] . "," . $dragonPos[1];

            if (isset($memo[$key])) {
                return $memo[$key];
            }

            if ($turn === "sheep") {

                if (empty($sheep)) {
                    return $memo[$key] = 1;
                }

                $total = 0;
                $moved = 0;

                foreach ($sheep as $i => [$r, $c]) {

                    if ($r == $ROWS - 1) {
                        $moved++;
                        continue;
                    }

                    if ($this->grid[$r + 1][$c] === "#" || $dragonPos !== [$r + 1, $c]) {

                        $moved++;

                        // move sheep down 1
                        $newSheep = $sheep;
                        $newSheep[$i] = [$r + 1, $c];
                        $newSheep = $this->normalizeSheep($newSheep);

                        $total += $count($newSheep, $dragonPos, "dragon");
                    }
                }

                if ($moved === 0) {
                    return $memo[$key] = $count($sheep, $dragonPos, "dragon");
                }

                return $memo[$key] = $total;
            }

            if ($turn === "dragon") {

                $total = 0;

                foreach ($dragonCanMoveTo($dragonPos) as [$r, $c]) {

                    $newSheep = [];
                    foreach ($sheep as [$sr, $sc]) {
                        if ($this->grid[$sr][$sc] === "#" || !($sr == $r && $sc == $c)) {
                            $newSheep[] = [$sr, $sc];
                        }
                    }
                    $newSheep = $this->normalizeSheep($newSheep);

                    $total += $count($newSheep, [$r, $c], "sheep");
                }

                return $memo[$key] = $total;
            }

            return 0;
        };

        return strval($count($sheep, $dragon, "sheep"));
    }
}
