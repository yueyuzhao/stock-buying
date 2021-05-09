<?php

require_once './Solution.php';

use PHPUnit\Framework\TestCase;

class SolutionTest extends TestCase
{
    function testCalc()
    {
        $solution = new Solution(
            [500, 200, 300],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertCount(2, $plans);

        $this->assertEquals([0, 1, 1], $plans[0]['hands']);
        $this->assertEquals(0, $plans[0]['left']);

        $this->assertEquals([1, 0, 0], $plans[1]['hands']);
        $this->assertEquals(0, $plans[0]['left']);
    }

    function testCalc2()
    {
        $solution = new Solution(
            [3, 2, 8],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc(2);
        $this->assertCount(2, $plans);

        $this->assertEquals([33, 100, 25], $plans[0]['hands']);
        $this->assertEquals(100, $plans[0]['left']);

        $this->assertEquals([34, 99, 25], $plans[1]['hands']);
        $this->assertEquals(0, $plans[1]['left']);
    }

    function testCalc3()
    {
        $solution = new Solution(
            [300, 400, 200],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertCount(1, $plans);
        $this->assertEquals([1, 0, 1], $plans[0]['hands']);
        $this->assertEquals(0, $plans[0]['left']);
    }

    function testCalc4()
    {
        $solution = new Solution(
            [2094, 313, 625],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertCount(1, $plans);
        $this->assertEquals([0, 1, 0], $plans[0]['hands']);
        $this->assertEquals(18700, $plans[0]['left']);
    }

    function testCalc5()
    {
        $solution = new Solution(
            [1, 1, 1],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertCount(1, $plans);
        $this->assertEquals([100, 200, 200], $plans[0]['hands']);
        $this->assertEquals(0, $plans[0]['left']);
    }

    function testCalc6()
    {
        $solution = new Solution(
            [50, 1, 1],
            [0.5, 0.2, 0.3],
            50000
        );
        $plans = $solution->calc();
        $this->assertEquals([5, 100, 150], $plans[0]['hands']);
        $this->assertEquals(0, $plans[0]['left']);
    }

    function testCalc7()
    {
        $solution = new Solution(
            [50, 1, 1],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertEquals([2, 200, 200], $plans[0]['hands']);
        $this->assertEquals(0, $plans[0]['left']);
    }

    function testCalc8()
    {
        $solution = new Solution(
            [299, 100],
            [0.6, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertCount(2, $plans);

        $this->assertEquals([1, 2], $plans[0]['hands']);
        $this->assertEquals(100, $plans[0]['left']);

        $this->assertEquals([0, 5], $plans[1]['hands']);
        $this->assertEquals(0, $plans[1]['left']);
    }

    function testCalc9()
    {
        $solution = new Solution(
            [600, 600, 600],
            [0.2, 0.4, 0.4],
            50000
        );
        $plans = $solution->calc();
        $this->assertCount(1, $plans);
        $this->assertEquals([0, 0, 0], $plans[0]['hands']);
        $this->assertEquals(50000, $plans[0]['left']);
    }

}
