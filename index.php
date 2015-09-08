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
ini_set('session.gc_maxlifetime', 604800);

/**
 * Main Execution
 */
if (Request::is_api_call()) {
	print "\nRESTful API call";
} else {
	Routing::route();
}