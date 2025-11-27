<?php

namespace Events\Y2025;

class Quest07
{
    private string $input1 = __DIR__ . '/inputs/Quest07/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest07/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest07/input3.txt';

    private array $names = [];
    private array $rules = [];

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $first = array_shift($lines);
        $this->names = array_map('trim', explode(',', $first));

        $this->rules = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            if (preg_match('/^\s*(\S)\s*>\s*(.+)$/', $line, $m)) {
                $left = $m[1];
                $right = $m[2];
                $allowed = array_map('trim', explode(',', $right));
                $this->rules[$left] = $allowed;
            }
        }
    }

    private function verifyRule(string $name): bool
    {
        $len = strlen($name);
        if ($len < 1) return false; // defensivo

        for ($i = 0; $i < $len - 1; $i++) {
            $cur = $name[$i];
            $next = $name[$i + 1];

            if (!isset($this->rules[$cur])) {
                return false;
            }
            if (!in_array($next, $this->rules[$cur], true)) {
                return false;
            }
        }

        return true;
    }

    public function solvePart1(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input1);

        foreach ($this->names as $name) {
            if ($this->verifyRule($name)) {
                return $name;
            }
        }

        return "Not found.\n";
    }

    public function solvePart2(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input2);

        $count = 0;
        foreach ($this->names as $key => $name) {
            if ($this->verifyRule($name)) {
                $count += $key + 1;
            }
        }

        return (string)$count;
    }

    public function solvePart3(?string $inputFile = null): string
    {
        $this->parse($inputFile ?? $this->input3);

        $minLen = 7;
        $maxLen = 11;

        $validPrefixes = [];
        foreach ($this->names as $p) {
            $p = trim($p);
            if ($this->verifyRule($p)) {
                if (strlen($p) <= $maxLen) {
                    $validPrefixes[] = $p;
                }
            }
        }

        if (empty($validPrefixes)) return "0";

        usort($validPrefixes, function($a, $b) {
            return strlen($a) <=> strlen($b);
        });

        $kept = [];
        foreach ($validPrefixes as $p) {
            $skip = false;
            foreach ($kept as $k) {
                if (strlen($k) <= strlen($p) && strncmp($p, $k, strlen($k)) === 0) {
                    $skip = true;
                    break;
                }
            }
            if (!$skip) $kept[] = $p;
        }

        $total = 0;

        foreach ($kept as $prefix) {
            $plen = strlen($prefix);
            if ($plen > $maxLen) continue;

            $dp = [];
            $last = substr($prefix, -1);
            $dp[$last] = 1;

            if ($plen >= $minLen && $plen <= $maxLen) {
                $total += 1;
            }

            for ($len = $plen; $len < $maxLen; $len++) {
                $nextDP = [];
                foreach ($dp as $ch => $cnt) {
                    if (!isset($this->rules[$ch])) continue;
                    foreach ($this->rules[$ch] as $nch) {
                        if (!isset($nextDP[$nch])) $nextDP[$nch] = 0;
                        $nextDP[$nch] += $cnt;
                    }
                }
                $dp = $nextDP;
                $newLen = $len + 1;
                if ($newLen >= $minLen) {
                    $total += array_sum($dp);
                }
            }
        }

        return (string)$total;
    }
}
