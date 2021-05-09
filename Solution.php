<?php

require_once('./helpers.php');

class Solution
{
    /**
     * @var int 一手股票的数量
     */
    public static $ONE_HAND = 100;

    /**
     * @var array 一手的股票价格数组
     */
    protected $prices = [];

    /**
     * @var array 股票仓位数组
     */
    protected $weights = [];

    /**
     * @var float 总预算
     */
    protected $total = 0.0;

    /**
     * Solution constructor.
     * @param $prices array 股票价格数组
     * @param $weights array 股票仓位数组
     * @param $total float 总预算
     */
    public function __construct(array $prices, array $weights, float $total)
    {
        foreach ($prices as $price) {
            $this->prices[] = $price * static::$ONE_HAND;
        }
        $this->weights = $weights;
        $this->total = $total;
    }

    /**
     * 计算最佳的购买购买方案
     * @param $maxPlans int 获取几个购买方案
     * @return array
     */
    public function calc($maxPlans = 5): array
    {
        if (!static::check()) {
            return [];
        }
        // 按照仓位配置比例，先购买
        $floorHand = PHP_INT_MAX;
        $floorIndex = 0;
        foreach ($this->prices as $i => $price) {
            $hand = floor($this->total * $this->weights[$i] / $price);
            if ($hand < $floorHand) {
                $floorHand = $hand;
                $floorIndex = $i;
            }
        }
        $plans = [];
        for ($i = 0; $i < 5; $i++) {
            if ($floorHand - $i < 0) {
                break;
            }
            $pre = $this->preBuy($floorIndex, $floorHand - $i);
            // 剩余金额中找出最小的组合
            $_plans = $this->getPlans($this->prices, $pre['left']);
            foreach ($_plans as $plan) {
                $plan['hands'] = array_add($pre['hands'], $plan['hands']);
                $signature = join('_', $plan['hands']);
                if (array_key_exists($signature, $plans)) {
                    continue;
                }
                $plan['dispersion'] = $this->calcDispersion($plan);
                $plans[$signature] = $plan;
            }
        }
        return static::findBestPlans($plans, $maxPlans);
    }

    /**
     * 检查数据是否合法
     * @return bool
     */
    private function check(): bool
    {
        if ($this->total <= 0) {
            return false;
        }
        if (count($this->prices) != count($this->weights)) {
            return false;
        }
        // 价格和份额不能为负值
        foreach ($this->prices as $i => $price) {
            if ($price < 0 || $this->weights[$i] < 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 根据持仓比例，按照最低购买手数的购买方案
     * @param $index int 最少购买股票手数的索引
     * @param $hand int 购买的手数
     * @return array
     */
    private function preBuy(int $index, int $hand): array
    {
        $floorMoney = $this->prices[$index] * $hand;
        $hands = [];
        $left = $this->total;
        foreach ($this->weights as $i => $weight) {
            $price = $this->prices[$i];
            $hands[$i] = floor($floorMoney * $weight / $this->weights[$index] / $price);
            $left -= $hands[$i] * $price;
        }
        return [
            'hands' => $hands,
            'left' => $left,
        ];
    }

    /**
     * 都不买的情况是最差的策略，但是如果是客观原因导致的，结果集中已经包含这种情况
     * 但凡能买任意一手，都比全都不买更优，这种情况下无需返回都不买的方案
     * @param $prices array
     * @param $total float
     * @return array
     */
    private function getPlans(array $prices, float $total): array
    {
        if (empty($prices)) {
            return [];
        }
        // 判断剩余金额能否能买任意一手
        $can = false;
        $hands = [];
        foreach ($prices as $i => $price) {
            $hands[$i] = 0;
            if ($price <= $total) {
                $can = true;
            }
        }
        // 如果不能，则直接返回
        if (!$can) {
            return [
                [
                    'hands' => $hands,
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
                $item['hands'][0] += 1;
                $res[] = $item;
            }
        }
        // B: 不买第一个品种的情况
        array_shift($prices);
        $plans = $this->getPlans($prices, $total);
        foreach ($plans as $item) {
            // 把第一个品种的购买数量加入到数组中
            // 如果有买的情况，但是也有都不买的情况，
            array_unshift($item['hands'], 0);
            $res[] = $item;
        }
        return static::findMinLeftPlans($res);
    }

    /**
     * 查找最小剩余的方案，如果有多个相同剩余的方案，则一起返回
     * @param $plans
     * @return array
     */
    private function findMinLeftPlans($plans): array
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
     * 获取购买方案中最优的方案
     * @param $plans array
     * @param $count int 保留方案的个数
     * @return array
     */
    private function findBestPlans(array $plans, int $count): array
    {
        $res = [];
        foreach ($plans as $plan) {
            // 如果有钱花完了，并且满足给定的仓位配置的
            // 立即返回
            if ($plan['dispersion'] == 0 && $plan['left'] == 0) {
                return [$plan];
            }
            $insert = false;
            foreach ($res as $i => $p) {
                if ($plan['dispersion'] <= $p['dispersion']) {
                    $insert = true;
                    array_splice($res, $i, 0, [$plan]);
                    break;
                }
            }
            if (!$insert) {
                $res[] = $plan;
            }
        }
        array_splice($res, $count);
        return $res;
    }

    /**
     * 计算购买方案的偏离度
     * @param $plan array 购买方案
     * @return float|int|object
     */
    private function calcDispersion(array $plan)
    {
        $totalSpent = $this->total - $plan['left'];
        if ($totalSpent == 0) {
            return PHP_INT_MAX;
        }
        $actualWeights = [];
        $hands = $plan['hands'];
        foreach ($this->prices as $i => $price) {
            $actualWeights[$i] = $price * $hands[$i] / $totalSpent;
        }
        $dispersion = 0;
        foreach ($this->weights as $i => $weight) {
            $dispersion += pow($weight * 10 - $actualWeights[$i] * 10, 2);
        }
        return $dispersion;
    }
}
