<?php

require_once('./helpers.php');

class Solution
{
    public static $ONE_HAND = 100;

    /**
     * 计算最佳的购买购买方案
     * @param $stocks array 股票价格和股票的配置仓位比例
     * @param $total int 预计花费的总金额
     * @return array
     */
    public static function calc(array $stocks, int $total): array
    {
        if (!static::check($stocks, $total)) {
            return [];
        }
        $prices = [];
        foreach ($stocks[0] as $price) {
            $prices[] = $price * static::$ONE_HAND;
        }
        // 按照仓位配置比例，先购买
        $floorHand = PHP_INT_MAX;
        $floorIndex = 0;
        $weights = $stocks[1];
        foreach ($prices as $i => $price) {
            $hand = floor($total * $weights[$i] / $price);
            if ($hand < $floorHand) {
                $floorHand = $hand;
                $floorIndex = $i;
            }
        }
        $floorMoney = $prices[$floorIndex] * $floorHand;
        $hands = [];
        $left = $total;
        foreach ($weights as $i => $weight) {
            $price = $prices[$i];
            $hands[$i] = floor($floorMoney * $weight / $weights[$floorIndex] / $price);
            $left -= $hands[$i] * $price;
        }
        // 剩余金额中找出最小的组合
        $plans = static::getPlans($prices, $left);
        // 合并已经买的方案
        $res = [];
        foreach ($plans as $plan) {
            $plan['hand'] = array_add($hands, $plan['hand']);
            $res[] = $plan;
        }
        return static::findBestPlan($res, $stocks, $total);
    }

    /**
     * 检查数据是否合法
     * @param $stocks
     * @param $total
     * @return bool
     */
    protected static function check($stocks, $total): bool
    {
        if (!is_array($stocks) || count($stocks) != 2 || $total < 0) {
            return false;
        }
        list($prices, $weights) = $stocks;
        if (count($prices) != count($weights) || array_sum($weights) > 1) {
            return false;
        }
        // 价格和份额不能为负值
        foreach ($prices as $i => $price) {
            if ($price < 0 || $weights[$i] < 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 都不买的情况是最差的策略，但是如果是客观原因导致的，结果集中已经包含这种情况
     * 但凡能买任意一手，都比全都不买更优，这种情况下无需返回都不买的方案
     * @param $prices
     * @param $total
     * @return array|array[]
     */
    public static function getPlans($prices, $total): array
    {
        if (empty($prices)) {
            return [];
        }
        // 判断剩余金额能否能买任意一手
        $can = false;
        $hand = [];
        foreach ($prices as $i => $price) {
            $hand[$i] = 0;
            if ($price <= $total) {
                $can = true;
            }
        }
        // 如果不能，则直接返回
        if (!$can) {
            return [
                [
                    'hand' => $hand,
                    'left' => $total,
                ],
            ];
        }
        $res = [];
        // A: 买一手第一个品种
        $oneHandMoney = $prices[0];
        $left = $total - $oneHandMoney;
        // 买完一手还有剩余
        if ($left >= 0) {
            $plans = static::getPlans($prices, $left);
            foreach ($plans as $item) {
                $item['hand'][0] += 1;
                $res[] = $item;
            }
        }
        // B: 不买第一个品种的情况
        array_shift($prices);
        $plans = static::getPlans($prices, $total);
        foreach ($plans as $item) {
            // 把第一个品种的购买数量加入到数组中
            // 如果有买的情况，但是也有都不买的情况，
            array_unshift($item['hand'], 0);
            $res[] = $item;
        }
        return static::findMinLeftPlans($res);
    }

    /**
     * 查找最小剩余的方案，如果有多个相同剩余的方案，则一起返回
     * @param $plans
     * @return array
     */
    public static function findMinLeftPlans($plans): array
    {
        $minLeft = PHP_INT_MAX;
        $res = [];
        foreach ($plans as $plan) {
            if ($plan['left'] > $minLeft) {
                continue;
            }
            if ($plan['left'] < $minLeft) {
                $minLeft = $plan['left'];
                $res = [];
            }
            $res[] = $plan;
        }
        return $res;
    }

    /**
     * 查找与用户设定的持仓比例最接近的购买方案
     * @param $plans
     * @param $stocks
     * @param $total
     * @return array
     */
    public static function findBestPlan($plans, $stocks, $total): array
    {
        if (empty($plans)) {
            return [];
        }
        if (count($plans) == 1) {
            return $plans[0];
        }
        $bestPlan = [];
        $minDispersion = PHP_INT_MAX;
        list($prices, $weights) = $stocks;
        foreach ($plans as $plan) {
            $totalSpent = $total - $plan['left'];
            $hand = $plan['hand'];
            $newWeights = [];
            foreach ($prices as $i => $price) {
                $newWeights[$i] = $price * static::$ONE_HAND * $hand[$i] / $totalSpent;
            }
            $plan['dispersion'] = static::calcDispersion($weights, $newWeights);
            // echo "\n" . $plan['dispersion'];
            if ($plan['dispersion'] < $minDispersion) {
                $minDispersion = $plan['dispersion'];
                $bestPlan = $plan;
            }
        }
        return $bestPlan;
    }

    /**
     * 计算实际仓位比例与期望仓位比例的偏离度
     * @param $weights
     * @param $actuals
     * @return float
     */
    protected static function calcDispersion($weights, $actuals)
    {
        $dispersion = 0;
        foreach ($weights as $i => $weight) {
            $dispersion += pow($weight * 10 - $actuals[$i] * 10, 2);
        }
        return $dispersion;
    }
}
