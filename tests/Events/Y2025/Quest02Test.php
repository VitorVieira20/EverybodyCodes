<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest02;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest02Test extends TestCase
{
    private Quest02 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest02();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest02_p1.txt';

        $expectedResult = "[357,862]";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest02_p2.txt';

        $expectedResult = '4076';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }
}