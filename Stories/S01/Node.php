<?php

namespace Stories\S01;

class Node
{
    public int $id;
    public int $value;
    public string $letter;
    public ?Node $left = null;
    public ?Node $right = null;

    public function __construct(int $id, int $value, string $letter)
    {
        $this->id = $id;
        $this->value = $value;
        $this->letter = $letter;
    }
}