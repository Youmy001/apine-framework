<?php
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

require_once('lib/core/autoloader.php');
Autoload::load_kernel();

function session(){
	// Start Session
	static $session;
	
	if($session==null){
		$session=new ApineSession();
		//print 'session ';
	}
	return $session;
}

Routing::route();
?>