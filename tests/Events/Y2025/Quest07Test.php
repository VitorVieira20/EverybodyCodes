<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest07;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest07Test extends TestCase
{
    private Quest07 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest07();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest07_p1.txt';

        $expectedResult = "Oroneth";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest07_p2.txt';

        $expectedResult = '23';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest07_p3_1.txt';

        $expectedResult = "25";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));

        $inputFile = $this->fixturesPath . 'quest07_p3_2.txt';

        $expectedResult = "1154";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}