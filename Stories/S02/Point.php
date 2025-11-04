<?php

namespace Stories\S02;

class Point
{
    public int $x;
    public int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function hash(): string
    {
        return $this->x . ',' . $this->y;
    }
}