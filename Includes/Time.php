<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/27
 * Time: 20:41
 */
function apine_execution_time () {

	static $before;
	$return = '';

	if (is_null($before)) {
		$before = microtime(true) * 1000;
	} else {
		$after = microtime(true) * 1000;
		$time = number_format($after - $before, 1);

		$return = $time;
	}

	return $return;

}