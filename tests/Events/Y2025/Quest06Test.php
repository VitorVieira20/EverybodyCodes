<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest06;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest06Test extends TestCase
{
    private Quest06 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest06();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest06_p1.txt';

        $expectedResult = "5";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest06_p2.txt';

        $expectedResult = '11';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest06_p3.txt';

        $expectedResult = "34";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile, 1, 10));

        $expectedResult = "72";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile, 2, 10));

        $expectedResult = "3442321";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile, 1000, 1000));
    }
}