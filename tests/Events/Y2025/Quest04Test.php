<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest04;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest04Test extends TestCase
{
    private Quest04 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest04();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest04_p1_1.txt';

        $expectedResult = "32400";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));

        $inputFile = $this->fixturesPath . 'quest04_p1_2.txt';

        $expectedResult = "15888";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest04_p2_1.txt';

        $expectedResult = '625000000000';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));

        $inputFile = $this->fixturesPath . 'quest04_p2_2.txt';

        $expectedResult = '1274509803922';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest04_p3_1.txt';

        $expectedResult = "400";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));

        $inputFile = $this->fixturesPath . 'quest04_p3_2.txt';

        $expectedResult = "6818";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}