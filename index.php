<?php
/**
 * APIne Framework Main Execution
 * This script runs basic environment setup and launches userside code
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
$before=microtime(true) * 1000;

require_once('lib/core/autoloader.php');
Autoload::load_kernel();

ini_set('display_errors',Config::get('apine-framework', 'display_errors'));
ini_set('include_path', realpath(dirname(__FILE__)));

if (Config::get('apine-framework', 'mode')=='development') {
	error_reporting(E_ALL | E_STRICT);
} else {
	error_reporting(E_ERROR);
}

date_default_timezone_set(Config::get('dateformat', 'timezone'));

if (!function_exists('str_split_unicode')) {
	/**
	 * A split method that supports unicode characters
	 * @param string $str
	 * @param number $l
	 * @return string
	 */
	function str_split_unicode($str, $l = 0) {
		
		if ($l > 0) {
			
			$ret = array();
			$len = mb_strlen($str, "UTF-8");
			
			for ($i = 0; $i < $len; $i += $l) {
				$ret[] = mb_substr($str, $i, $l, "UTF-8");
			}
			
			return $ret;
	    }
	    
	    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	    
	}
	
}

/**
 * Calculate the total execution time 
 * of the request up to now
 * @return string
 */
function execution_time() {
	
	global $before;
	$after=microtime(true) * 1000;
	return number_format((($after-$before)),1);
	
}

/**
 * Main Execution
 */
if (Request::is_api_call()) {
	print "\nRESTful API call";
} else {
	Routing::route();
}