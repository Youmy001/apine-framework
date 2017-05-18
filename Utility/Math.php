<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/25
 * Time: 14:50
 */

namespace Apine\Utility;


class Math {

	/**
	 * Compute a ratio from a multiplier
	 *
	 * @param double $a_float
	 *        Ratio multiplier
	 * @param float $a_tolerance
	 *        Precision level of the procedure
	 * @return string
	 */
	public static function float_to_ratio ($a_float, $a_tolerance = 1.e-6) {

		$h1 = 1;
		$h2 = 0;
		$k1 = 0;
		$k2 = 1;
		$b = 1 / $a_float;

		do {
			$b = 1 / $b;
			$a = floor($b);
			$aux = $h1;
			$h1 = $a * $h1 + $h2;
			$h2 = $aux;
			$aux = $k1;
			$k1 = $a * $k1 + $k2;
			$k2 = $aux;
			$b = $b - $a;
		} while (abs($a_float - $h1 / $k1) > $a_float * $a_tolerance);

		return "$h1/$k1";

	}

}