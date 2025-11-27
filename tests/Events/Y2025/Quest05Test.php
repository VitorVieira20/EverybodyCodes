<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest05;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest05Test extends TestCase
{
    private Quest05 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest05();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest05_p1.txt';

        $expectedResult = "581078";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest05_p2.txt';

        $expectedResult = '77053';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest05_p3_1.txt';

        $expectedResult = "260";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));

        $inputFile = $this->fixturesPath . 'quest05_p3_2.txt';

        $expectedResult = "4";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}