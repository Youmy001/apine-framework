<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/25
 * Time: 14:37
 */

namespace Apine\Utility;


class StringManipulation {

	/**
	 * A split method that supports unicode characters
	 *
	 * @param string $a_string
	 * @param integer $a_length
	 * @return string
	 *
	 * @author qerery <qeremy@gmail.com>
	 * @see http://us.php.net/str_split#107658
	 */
	public static function split_unicode ($a_string, $a_length = 0) {

		if ($a_length > 0) {

			$ret = array();
			$a_lengthen = mb_strlen($a_string, "UTF-8");

			for ($i = 0;$i < $a_lengthen;$i += $a_length) {
				$ret[] = mb_substr($a_string, $i, $a_length, "UTF-8");
			}

			return $ret;
		}

		return preg_split("//u", $a_string, -1, PREG_SPLIT_NO_EMPTY);

	}

}