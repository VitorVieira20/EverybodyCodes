<?php

namespace Stories\S01;

use Stories\S01\Node;

function insertBST(?Node $root, Node $newNode): Node
{
    if ($root === null) {
        return $newNode;
    }

    if ($newNode->value < $root->value) {
        $root->left = insertBST($root->left, $newNode);
    } else {
        $root->right = insertBST($root->right, $newNode);
    }

    return $root;
}


function findNode(?Node $root, int $id): ?array
{
    if ($root === null) {
        return null;
    }

    if ($root->id === $id) {
        return [$root->value, $root->letter];
    }

    $leftResult = findNode($root->left, $id);
    if ($leftResult !== null) {
        return $leftResult;
    }

    return findNode($root->right, $id);
}


function swapNode(?Node $root, int $id, int $value, string $letter)
{
    if ($root === null) {
        return null;
    }

    if ($root->id === $id) {
        $root->value = $value;
        $root->letter = $letter;
        return [$root->value, $root->letter];
    }

    $leftResult = swapNode($root->left, $id, $value, $letter);
    if ($leftResult !== null) {
        return $leftResult;
    }

    return swapNode($root->right, $id, $value, $letter);
}


function findNodesAndParents(?Node $root, ?Node $parent, int $id, array &$found)
{
    if ($root === null)
        return;

    if ($root->id === $id) {
        $found[] = ['node' => $root, 'parent' => $parent];
    }

    findNodesAndParents($root->left, $root, $id, $found);
    findNodesAndParents($root->right, $root, $id, $found);
}


function swapSubtrees(?Node &$root1, ?Node &$root2, int $id)
{
    $found1 = [];
    $found2 = [];

    findNodesAndParents($root1, null, $id, $found1);
    findNodesAndParents($root2, null, $id, $found2);

    if (empty($found1) && empty($found2)) {
        return;
    }

    if (!empty($found1) && !empty($found2)) {
        $node1 = $found1[0]['node'];
        $parent1 = $found1[0]['parent'];
        $node2 = $found2[0]['node'];
        $parent2 = $found2[0]['parent'];

        if ($parent1 === null) {
            $root1 = $node2;
        } else {
            if ($parent1->left === $node1)
                $parent1->left = $node2;
            else
                $parent1->right = $node2;
        }

        if ($parent2 === null) {
            $root2 = $node1;
        } else {
            if ($parent2->left === $node2)
                $parent2->left = $node1;
            else
                $parent2->right = $node1;
        }

        return;
    }

    if (count($found1) >= 2) {
        $nodeA = $found1[0]['node'];
        $parentA = $found1[0]['parent'];
        $nodeB = $found1[1]['node'];
        $parentB = $found1[1]['parent'];

        if ($parentA === null) {
            $root1 = $nodeB;
        } else {
            if ($parentA->left === $nodeA)
                $parentA->left = $nodeB;
            else
                $parentA->right = $nodeB;
        }

        if ($parentB === null) {
            $root1 = $nodeA;
        } else {
            if ($parentB->left === $nodeB)
                $parentB->left = $nodeA;
            else
                $parentB->right = $nodeA;
        }

        return;
    }

    if (count($found2) >= 2) {
        $nodeA = $found2[0]['node'];
        $parentA = $found2[0]['parent'];
        $nodeB = $found2[1]['node'];
        $parentB = $found2[1]['parent'];

        if ($parentA === null) {
            $root2 = $nodeB;
        } else {
            if ($parentA->left === $nodeA)
                $parentA->left = $nodeB;
            else
                $parentA->right = $nodeB;
        }

        if ($parentB === null) {
            $root2 = $nodeA;
        } else {
            if ($parentB->left === $nodeB)
                $parentB->left = $nodeA;
            else
                $parentB->right = $nodeA;
        }
    }
}


function getLevelWithMostNodes(?Node $root): string
{
    if ($root === null)
        return '';

    $queue = [[$root, 0]];
    $levels = [];

    while (!empty($queue)) {
        [$node, $level] = array_shift($queue);
        $levels[$level][] = $node->letter;

        if ($node->left !== null)
            $queue[] = [$node->left, $level + 1];
        if ($node->right !== null)
            $queue[] = [$node->right, $level + 1];
    }

    $maxLevel = 0;
    $maxCount = 0;

    foreach ($levels as $level => $letters) {
        if (count($letters) > $maxCount) {
            $maxCount = count($letters);
            $maxLevel = $level;
        }
    }

    return implode('', $levels[$maxLevel]);
}