<?php

/**
 * 调试：打印传递的参数并退出
 */
function dd()
{
	foreach (func_get_args() as $arg) {
		var_dump($arg);
	}
	die;
}

/**
 * 两个数组内的值分别相加
 * @param $a1 array
 * @param $a2 array
 * @return array
 */
function array_add(array $a1, array $a2): array
{
	$res = [];
	foreach ($a1 as $key => $value) {
		$res[$key] = $value + $a2[$key];
	}
	return $res;
}
