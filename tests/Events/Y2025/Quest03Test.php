<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest03;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest03Test extends TestCase
{
    private Quest03 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest03();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest03_p1.txt';

        $expectedResult = "29";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest03_p2.txt';

        $expectedResult = '781';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest03_p3.txt';

        $expectedResult = "3";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}