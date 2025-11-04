<?php

namespace Stories\S01;

include __DIR__ . '/utils.php';

use Stories\S01\Node;


class Quest02
{
    private string $input1 = __DIR__ . '/../inputs/S01/Quest02/input1.txt';
    private string $input2 = __DIR__ . '/../inputs/S01/Quest02/input2.txt';
    private string $input3 = __DIR__ . '/../inputs/S01/Quest02/input3.txt';

    private ?Node $leftRoot = null;
    private ?Node $rightRoot = null;
    private array $instructions = [];


    public function __construct()
    {
    }


    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $this->instructions = [];

        foreach ($lines as $line) {

            if (str_starts_with($line, 'ADD')) {
                preg_match('/ADD id=(\d+) left=\[(\d+),([^\]]+)\] right=\[(\d+),([^\]]+)\]/', $line, $m);

                $this->instructions[] = [
                    'method' => 'add',
                    'id' => (int) $m[1],
                    'left' => ['value' => (int) $m[2], 'letter' => $m[3]],
                    'right' => ['value' => (int) $m[4], 'letter' => $m[5]],
                ];
            }

            if (str_starts_with($line, 'SWAP')) {
                preg_match('/SWAP (\d+)/', $line, $m);
                $this->instructions[] = ['method' => 'swap', 'id' => (int) $m[1]];
            }
        }
    }

    private function applyInstruction(array $inst, bool $isPart3): void
    {
        if ($inst['method'] === 'add') {
            $this->leftRoot = insertBST($this->leftRoot, new Node($inst['id'], $inst['left']['value'], $inst['left']['letter']));
            $this->rightRoot = insertBST($this->rightRoot, new Node($inst['id'], $inst['right']['value'], $inst['right']['letter']));
            return;
        }

        if ($isPart3) {
            swapSubtrees($this->leftRoot, $this->rightRoot, $inst['id']);
            return;
        }

        [$lv, $ll] = findNode($this->leftRoot, $inst['id']);
        [$rv, $rl] = findNode($this->rightRoot, $inst['id']);

        swapNode($this->leftRoot, $inst['id'], $rv, $rl);
        swapNode($this->rightRoot, $inst['id'], $lv, $ll);
    }

    private function build(bool $isPart3 = false): void
    {
        $this->leftRoot = $this->rightRoot = null;

        foreach ($this->instructions as $inst) {
            $this->applyInstruction($inst, $isPart3);
        }
    }

    private function output(): string
    {
        return getLevelWithMostNodes($this->leftRoot)
             . getLevelWithMostNodes($this->rightRoot);
    }

    public function solvePart1(): string
    {
        $this->parse($this->input1);
        $this->build(false);
        return $this->output();
    }


    public function solvePart2(): string
    {
        $this->parse($this->input2);
        $this->build(false);
        return $this->output();
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);
        $this->build(true);
        return $this->output();
    }
}
