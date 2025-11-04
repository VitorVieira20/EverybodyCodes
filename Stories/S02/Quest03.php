<?php

namespace Stories\S02;

class Quest03
{
    private string $input1 = __DIR__ . '/../inputs/S02/Quest03/input1.txt';
    private string $input2 = __DIR__ . '/../inputs/S02/Quest03/input2.txt';
    private string $input3 = __DIR__ . '/../inputs/S02/Quest03/input3.txt';

    /** @var Dice[] */
    private array $dice = [];
    private array $sequence = [];


    public function __construct()
    {
    }

    private function parse(string $filePath): void
    {
        $this->dice = [];

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '')
                continue;

            if (str_contains($line, 'faces=') && str_contains($line, 'seed=')) {

                [$idPart, $rest] = array_map('trim', explode(':', $line, 2));
                $id = intval($idPart);

                $facesStr = substr(
                    $rest,
                    strpos($rest, '[') + 1,
                    strpos($rest, ']') - strpos($rest, '[') - 1
                );

                $faces = array_map('intval', array_map('trim', explode(',', $facesStr)));

                $seed = intval(substr($rest, strrpos($rest, '=') + 1));

                $this->dice[] = new Dice($id, $faces, $seed);

            } else {
                $this->sequence = [];

                foreach (str_split($line) as $char) {
                    $this->sequence[] = intval($char);
                }

                foreach ($this->dice as $die) {
                    $die->setSequence($this->sequence);
                }
            }
        }
    }


    private function parseGrid(string $filePath): array
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $grid = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '')
                continue;

            if (str_contains($line, "faces=") && str_contains($line, "seed=")) {
                continue;
            }

            $row = [];
            foreach (str_split($line) as $char) {
                if (ctype_digit($char)) {
                    $row[] = intval($char);
                }
            }

            $grid[] = $row;
        }

        return $grid;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);

        $goal = 10000;
        $totalPoints = 0;
        $rollNumber = 0;

        while ($totalPoints < $goal) {
            $rollPoints = 0;

            foreach ($this->dice as $die) {
                $rollPoints += $die->roll();
            }

            $totalPoints += $rollPoints;
            $rollNumber++;
        }

        return $rollNumber;
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);

        $finished = 0;
        $diceSize = count($this->dice);
        $finishOrder = [];

        while ($finished < $diceSize) {
            $toRemove = [];

            foreach ($this->dice as $die) {
                if ($die->isFinished()) {
                    $finishOrder[] = $die->id;
                    $finished++;
                    $toRemove[] = $die;
                }
            }

            $this->dice = array_filter(
                $this->dice,
                fn($d) => !in_array($d, $toRemove, true)
            );

            foreach ($this->dice as $die) {
                $die->roll();
            }
        }

        return implode(",", $finishOrder);
    }



    public function solvePart3(): int
    {
        $this->parse($this->input3);

        $gridList = $this->parseGrid($this->input3);
        $rows = count($gridList);
        $cols = count($gridList[0]);

        $grid = $gridList;

        $aggregate = [];

        foreach ($this->dice as $die) {
            $value = $die->roll();

            $possible = [];

            for ($r = 0; $r < $rows; $r++) {
                for ($c = 0; $c < $cols; $c++) {
                    if ($grid[$r][$c] === $value) {
                        $p = new Point($r, $c);
                        $possible[$p->hash()] = $p;
                    }
                }
            }

            $aggregate += $possible;

            while (!empty($possible)) {
                $value = $die->roll();
                $nextSet = [];

                foreach ($possible as $p) {
                    $r = $p->x;
                    $c = $p->y;

                    $deltas = [
                        [0, 0],
                        [1, 0],
                        [-1, 0],
                        [0, 1],
                        [0, -1]
                    ];

                    foreach ($deltas as [$dr, $dc]) {
                        $nr = $r + $dr;
                        $nc = $c + $dc;

                        if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols) {
                            continue;
                        }

                        if ($grid[$nr][$nc] !== $value) {
                            continue;
                        }

                        $np = new Point($nr, $nc);
                        $nextSet[$np->hash()] = $np;
                    }
                }

                $possible = $nextSet;
                $aggregate += $possible;
            }
        }

        return count($aggregate);
    }

}