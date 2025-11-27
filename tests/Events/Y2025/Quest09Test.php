<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest09;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest09Test extends TestCase
{
    private Quest09 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest09();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest09_p1.txt';

        $expectedResult = "414";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest09_p2.txt';

        $expectedResult = '1245';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest09_p3_1.txt';

        $expectedResult = "12";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));

        $inputFile = $this->fixturesPath . 'quest09_p3_2.txt';

        $expectedResult = "36";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}