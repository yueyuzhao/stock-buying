<?php

function dd()
{
	foreach (func_get_args() as $arg) {
		var_dump($arg);
	}
	die;
}

function array_add($a1, $a2)
{
	$res = [];
	foreach ($a1 as $key => $value) {
		$res[$key] = $value + $a2[$key];
	}
	return $res;
}