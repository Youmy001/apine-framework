<?php
/**
 * APIne Framework Main Execution
 * This script runs basic environment setup and launches userside code
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
ini_set('display_errors','On');
ini_set('include_path', realpath(dirname(__FILE__)));
error_reporting(E_ALL | E_STRICT);

if(!function_exists('str_split_unicode')){
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

$before=microtime(true);

require_once('lib/core/autoloader.php');
Autoload::load_kernel();

/**
 * Returns current session state in any scope of the Framework
 * @return ApineSession
 */
function session(){
	// Start Session
	static $session;
	
	if($session==null){
		$session=new ApineSession();
	}
	return $session;
}

Routing::route();

function execution_time(){
	global $before;
	
	$after=microtime(true);
	
	return number_format((($after-$before)*1),4);
}

?>