<?php

namespace Tests\Events\Y2025;

use Events\Y2025\Quest08;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Quest08Test extends TestCase
{
    private Quest08 $quest;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->quest = new Quest08();
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }


    #[Test]
    public function it_solves_part_1_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest08_p1.txt';

        $expectedResult = "4";

        $this->assertEquals($expectedResult, $this->quest->solvePart1($inputFile));
    }

    #[Test]
    public function it_solves_part_2_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest08_p2.txt';

        $expectedResult = '21';

        $this->assertEquals($expectedResult, $this->quest->solvePart2($inputFile));
    }

    #[Test]
    public function it_solves_part_3_example_correctly()
    {
        $inputFile = $this->fixturesPath . 'quest08_p3.txt';

        $expectedResult = "6";

        $this->assertEquals($expectedResult, $this->quest->solvePart3($inputFile));
    }
}