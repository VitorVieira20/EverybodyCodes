<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest01;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest01Test extends TestCase
{
    private Quest01 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest01();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest01_p1.txt';

        $expectedResult = 'Fyrryn';

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest01_p2.txt';

        $expectedResult = 'Elarzris';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest01_p3.txt';

        $expectedResult = 'Drakzyph';

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}