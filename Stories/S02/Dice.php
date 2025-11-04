<?php

namespace Stories\S02;

class Dice
{
    public int $id;
    public array $faces;
    public int $seed;

    public ?array $sequence = null;

    public int $pulse;
    public int $rollNumber;
    public int $currentIndex;

    public function __construct(int $id, array $faces, int $seed)
    {
        $this->id = $id;
        $this->faces = $faces;
        $this->seed = $seed;
        $this->pulse = $seed;
        $this->rollNumber = 1;
        $this->currentIndex = 0;
    }

    public function roll()
    {
        $spin = $this->rollNumber * $this->pulse;
        $this->currentIndex = ($this->currentIndex + $spin) % count($this->faces);

        $result = $this->faces[$this->currentIndex];

        if (!empty($this->sequence) && $this->sequence[0] === $result) {
            array_shift($this->sequence);
        }

        $this->pulse = ($this->pulse + $spin) % $this->seed;
        $this->pulse += 1 + $this->rollNumber + $this->seed;

        $this->rollNumber++;

        return $result;
    }

    public function setSequence(array $sequence): void
    {
        $this->sequence = $sequence;
    }

    public function isFinished(): bool
    {
        return empty($this->sequence);
    }
}
