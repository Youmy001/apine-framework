<?php
/**
 * APIne Framework Main Execution
 * This script runs basic environment setup and launches userside code
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
$before = microtime(true) * 1000;

require_once('lib/core/autoloader.php');
Autoload::load_kernel();
ini_set('include_path', realpath(dirname(__FILE__)));

if (Config::get('apine-framework', 'mode') == 'development') {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
} else {
	ini_set('display_errors', 'Off');
	error_reporting(E_ERROR);
}

date_default_timezone_set(Config::get('dateformat', 'timezone'));

/**
 * Calculate the total execution time 
 * of the request up to now
 * @return string
 */
function execution_time () {
	
	global $before;
	$after = microtime(true) * 1000;
	return number_format($after - $before, 1);
	
}

//$locale = new TranslationLocale(ApineSession::language());
//print ($locale->timezone());
//date_default_timezone_set($locale->timezone());
//var_dump(ApineSession::translation());

//print (date($locale->datehour(),time("now")));

//var_dump(is_numeric("2015-09-10 10:30:00"));
//var_dump(is_timestamp("2015-09-10 10:30:00 +00:00"));

//print strtotime("2015-09")."\n";
//print strtotime("2015-09-10 10:30")."\n";
//print strtotime("2015-09-10 10:30:00")."\n";
//print strtotime("2015-09-10 10:30:00 EDT")."\n";
//print (date($locale->datehour(), time("now"))."\n");
//print (date($locale->datehour(), strtotime("2015-09-10 10:30:00 GMT")));

/*$locale = new TranslationLocale(ApineSession::language());
$datetime = new DateTime($locale->timezone());
var_dump("Raw Offset (seconds) = ".$locale->offset());
var_dump("ISO Offset (Signed Hours) = ".$locale->iso_offset());*/

$class = new ConcreteClass();

var_dump($class->session());


/**
 * Main Execution
 */
if (Request::is_api_call()) {
	print "\nRESTful API call";
} else {
	Routing::route();
}