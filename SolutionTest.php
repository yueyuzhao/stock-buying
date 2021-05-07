<?php

require_once './Solution.php';

use PHPUnit\Framework\TestCase;

class SolutionTest extends TestCase
{
    function testCalc()
    {
        $plan = Solution::calc([
            [500, 200, 300],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([0, 1, 1], $plan['hand']);
        $this->assertEquals(0, $plan['left']);
    }

    function testCalc2()
    {
        $plan = Solution::calc([
            [3, 2, 8],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([33, 100, 25], $plan['hand']);
        $this->assertEquals(100, $plan['left']);
    }

    function testCalc3()
    {
        $plan = Solution::calc([
            [300, 400, 200],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([1, 0, 1], $plan['hand']);
        $this->assertEquals(0, $plan['left']);
    }

    function testCalc4()
    {
        $plan = Solution::calc([
            [2094, 313, 625],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([0, 1, 0], $plan['hand']);
        $this->assertEquals(18700, $plan['left']);
    }

    function testCalc5()
    {
        $plan = Solution::calc([
            [1, 1, 1],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([100, 200, 200], $plan['hand']);
        $this->assertEquals(0, $plan['left']);
    }

    function testCalc6()
    {
        $plan = Solution::calc([
            [50, 1, 1],
            [0.5, 0.2, 0.3],
        ], 50000);
        $this->assertEquals([5, 100, 150], $plan['hand']);
        $this->assertEquals(0, $plan['left']);
    }

    function testCalc7()
    {
        $plan = Solution::calc([
            [50, 1, 1],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([2, 200, 200], $plan['hand']);
        $this->assertEquals(0, $plan['left']);
    }

    function testCalc8()
    {
        $plan = Solution::calc([
            [299, 100],
            [0.6, 0.4],
        ], 50000);
        $this->assertEquals([1, 2], $plan['hand']);
        $this->assertEquals(100, $plan['left']);
    }

    function testCalc9()
    {
        $plan = Solution::calc([
            [600, 600, 600],
            [0.2, 0.4, 0.4],
        ], 50000);
        $this->assertEquals([0, 0, 0], $plan['hand']);
        $this->assertEquals(50000, $plan['left']);
    }

    function testGetPlans()
    {
        $res = Solution::getPlans([60000, 70000, 80000], 50000);
        $this->assertEquals(1, count($res));
        $this->assertEquals([
            'hand' => [0, 0, 0],
            'left' => 50000,
        ], $res[0]);

        $res = Solution::getPlans([50000, 60000, 70000], 50000);
        $this->assertEquals(1, count($res));
        $this->assertEquals([
            'hand' => [1, 0, 0],
            'left' => 0,
        ], $res[0]);

        $res = Solution::getPlans([50000, 30000, 20000], 50000);
        $this->assertEquals(2, count($res));
        $this->assertEquals([
            'hand' => [1, 0, 0],
            'left' => 0,
        ], $res[0]);
        $this->assertEquals([
            'hand' => [0, 1, 1],
            'left' => 0,
        ], $res[1]);

        $res = Solution::getPlans([50000, 30000, 25000], 50000);
        $this->assertEquals(2, count($res));
        $this->assertEquals([
            'hand' => [1, 0, 0],
            'left' => 0,
        ], $res[0]);
        $this->assertEquals([
            'hand' => [0, 0, 2],
            'left' => 0,
        ], $res[1]);
    }

    function testFindMinLeftPlans()
    {
        $plans = [
            [
                'hand' => [1, 0, 0],
                'left' => 0,
            ],
            [
                'hand' => [0, 1, 0],
                'left' => 20000,
            ],
            [
                'hand' => [0, 0, 2],
                'left' => 0,
            ],
        ];
        $res = Solution::findMinLeftPlans($plans);
        $this->assertEquals(2, count($res));
        $this->assertEquals([
            'hand' => [1, 0, 0],
            'left' => 0,
        ], $res[0]);
        $this->assertEquals([
            'hand' => [0, 0, 2],
            'left' => 0,
        ], $res[1]);
    }

    function testFindBestPlan()
    {
        $plans = [
            [
                'hand' => [1, 0, 0],
                'left' => 0,
            ],
            [
                'hand' => [0, 0, 2],
                'left' => 0,
            ],
        ];
        $stocks = [
            [500, 300, 250],
            [0.2, 0.4, 0.4],
        ];
        $res = Solution::findBestPlan($plans, $stocks, 50000);
        $this->assertEquals([
            'hand' => [0, 0, 2],
            'left' => 0,
            'dispersion' => 56,
        ], $res);
    }
}
