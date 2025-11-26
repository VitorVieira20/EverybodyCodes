<?php

namespace Events\Y2025;

class Quest18
{
    private string $input1 = __DIR__ . '/inputs/Quest18/input1.txt';
    private string $input2 = __DIR__ . '/inputs/Quest18/input2.txt';
    private string $input3 = __DIR__ . '/inputs/Quest18/input3.txt';

    private array $plants = [];
    private array $testCases = [];
    private array $cache = [];
    private array $currentFreeBranchStates = [];
    private array $rootDependenciesCache = [];

    private function parse(string $filePath): void
    {
        $this->plants = [];
        $this->testCases = [];
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $currentPlantId = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^Plant (\d+) with thickness (-?\d+):/i', $line, $matches)) {
                $currentPlantId = (int)$matches[1];
                $this->plants[$currentPlantId] = ['thickness' => (int)$matches[2], 'inputs' => []];
            }
            elseif (preg_match('/branch to Plant (\d+) with thickness (-?\d+)/i', $line, $matches)) {
                if ($currentPlantId !== null) {
                    $this->plants[$currentPlantId]['inputs'][] = ['source' => (int)$matches[1], 'weight' => (int)$matches[2]];
                }
            }
            elseif (preg_match('/free branch with thickness (-?\d+)/i', $line, $matches)) {
                if ($currentPlantId !== null) {
                    $this->plants[$currentPlantId]['inputs'][] = ['source' => 'FREE', 'weight' => (int)$matches[1]];
                }
            }
            elseif (preg_match('/^[01 ]+$/', $line)) {
                $this->testCases[] = $line;
            }
        }
        ksort($this->plants);
    }


    private function getEnergy(int $plantId, array $pathStack = []): int
    {
        if (in_array($plantId, $pathStack)) {
            return 0;
        }

        if (isset($this->cache[$plantId])) {
            return $this->cache[$plantId];
        }

        if (!isset($this->plants[$plantId])) {
            return 0;
        }

        $plantData = $this->plants[$plantId];
        $totalIncomingEnergy = 0;
        
        $pathStack[] = $plantId;

        foreach ($plantData['inputs'] as $input) {
            $weight = $input['weight'];
            
            if ($input['source'] === 'FREE') {
                $isActive = $this->currentFreeBranchStates[$plantId] ?? 1;
                
                if ($isActive === 1) {
                    $totalIncomingEnergy += ($weight * 1);
                } else {
                    $totalIncomingEnergy += 0;
                }

            } else {
                $sourceEnergy = $this->getEnergy($input['source'], $pathStack);
                $totalIncomingEnergy += ($weight * $sourceEnergy);
            }
        }

        if ($totalIncomingEnergy < $plantData['thickness']) {
            $finalEnergy = 0;
        } else {
            $finalEnergy = $totalIncomingEnergy;
        }

        $this->cache[$plantId] = $finalEnergy;
        return $finalEnergy;
    }


    private function getRootsFeedingPlant(int $plantId): array
    {
        if (isset($this->rootDependenciesCache[$plantId])) return $this->rootDependenciesCache[$plantId];
        $roots = [];
        if (!isset($this->plants[$plantId])) return [];

        foreach ($this->plants[$plantId]['inputs'] as $input) {
            if ($input['source'] === 'FREE') {
                $roots[] = $plantId;
            } else {
                $childRoots = $this->getRootsFeedingPlant($input['source']);
                foreach ($childRoots as $r) $roots[] = $r;
            }
        }
        $roots = array_unique($roots);
        $this->rootDependenciesCache[$plantId] = $roots;
        return $roots;
    }


    private function identifyClusters(array $allRoots, int $lastPlantId): array
    {
        $parent = [];
        foreach ($allRoots as $r) $parent[$r] = $r;

        $find = function($i) use (&$parent) {
            if (!isset($parent[$i])) return $i;
            $path = [];
            while ($parent[$i] != $i) {
                $path[] = $i;
                $i = $parent[$i];
            }
            foreach ($path as $p) $parent[$p] = $i;
            return $i;
        };

        $union = function($i, $j) use (&$parent, $find) {
            $rootI = $find($i);
            $rootJ = $find($j);
            if ($rootI != $rootJ) $parent[$rootI] = $rootJ;
        };

        foreach ($this->plants as $plantId => $data) {
            if ($plantId === $lastPlantId) continue; 

            $roots = $this->getRootsFeedingPlant($plantId);
            if (count($roots) > 1) {
                $first = array_shift($roots);
                foreach ($roots as $r) {
                    $union($first, $r);
                }
            }
        }

        $clusters = [];
        foreach ($allRoots as $root) {
            $p = $find($root);
            $clusters[$p][] = $root;
        }
        return array_values($clusters);
    }


    private function hillClimb(array $rootsToOptimize, int $lastPlantId): int
    {
        $improved = true;
        
        $this->cache = [];
        $currentMax = $this->getEnergy($lastPlantId);

        while ($improved) {
            $improved = false;
            
            foreach ($rootsToOptimize as $rootId) {
                $originalState = $this->currentFreeBranchStates[$rootId] ?? 0;
                $newState = $originalState === 1 ? 0 : 1;
                
                $this->currentFreeBranchStates[$rootId] = $newState;
                $this->cache = [];
                $newEnergy = $this->getEnergy($lastPlantId);
                
                if ($newEnergy > $currentMax) {
                    $currentMax = $newEnergy;
                    $improved = true;
                } else {
                    $this->currentFreeBranchStates[$rootId] = $originalState;
                }
            }
        }
        
        return $currentMax;
    }


    public function solvePart3(): string
    {
        $this->parse($this->input3);
        $lastPlantId = max(array_keys($this->plants));
        
        $allRoots = [];
        foreach ($this->plants as $id => $data) {
            foreach ($data['inputs'] as $inp) {
                if ($inp['source'] === 'FREE') {
                    $allRoots[] = $id;
                    break;
                }
            }
        }
        sort($allRoots);

        $bestStartConfig = [];
        $maxStartEnergy = PHP_INT_MIN;

        foreach ($this->testCases as $caseLine) {
            $bits = str_split(str_replace(' ', '', $caseLine));
            $tempConfig = [];
            foreach ($allRoots as $index => $plantId) {
                $state = isset($bits[$index]) ? (int)$bits[$index] : 0;
                $tempConfig[$plantId] = $state;
            }
            
            $this->currentFreeBranchStates = $tempConfig;
            $this->cache = [];
            $energy = $this->getEnergy($lastPlantId);
            
            if ($energy > $maxStartEnergy) {
                $maxStartEnergy = $energy;
                $bestStartConfig = $tempConfig;
            }
        }
        
        $this->currentFreeBranchStates = $bestStartConfig;

        $clusters = $this->identifyClusters($allRoots, $lastPlantId);

        foreach ($clusters as $clusterRoots) {
            $this->hillClimb($clusterRoots, $lastPlantId);
        }

        $this->cache = [];
        $globalMaxEnergy = $this->getEnergy($lastPlantId);

        $totalDifference = 0;
        foreach ($this->testCases as $caseLine) {
            $bits = str_split(str_replace(' ', '', $caseLine));
            $this->currentFreeBranchStates = [];
            foreach ($allRoots as $index => $plantId) {
                $this->currentFreeBranchStates[$plantId] = isset($bits[$index]) ? (int)$bits[$index] : 0;
            }

            $this->cache = [];
            $energy = $this->getEnergy($lastPlantId);

            if ($energy > 0) {
                if ($energy > $globalMaxEnergy) {
                    $globalMaxEnergy = $energy;
                }
                $totalDifference += ($globalMaxEnergy - $energy);
            }
        }

        return (string)$totalDifference;
    }


    public function solvePart1(): string
    {
        $this->parse($this->input1);
        
        $this->currentFreeBranchStates = []; 
        
        if (empty($this->plants)) return "0";
        $lastPlantId = max(array_keys($this->plants));
        
        return (string)$this->getEnergy($lastPlantId);
    }

    
    public function solvePart2(): string
    {
        $this->parse($this->input2);

        if (empty($this->plants)) return "0";
        $lastPlantId = max(array_keys($this->plants));

        $plantsWithFreeBranches = [];
        foreach ($this->plants as $id => $data) {
            foreach ($data['inputs'] as $inp) {
                if ($inp['source'] === 'FREE') {
                    $plantsWithFreeBranches[] = $id;
                    break;
                }
            }
        }
        sort($plantsWithFreeBranches);

        $totalSum = 0;

        foreach ($this->testCases as $caseLine) {
            $bits = str_split(str_replace(' ', '', $caseLine));

            $this->currentFreeBranchStates = [];
            foreach ($plantsWithFreeBranches as $index => $plantId) {
                $bit = isset($bits[$index]) ? (int)$bits[$index] : 0;
                $this->currentFreeBranchStates[$plantId] = $bit;
            }

            $this->cache = [];

            $energy = $this->getEnergy($lastPlantId);
            
            $totalSum += $energy;
        }

        return (string)$totalSum;
    }
}