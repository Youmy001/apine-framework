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
ini_set('display_errors', 'On');
ini_set('include_path', realpath(dirname(__FILE__)));
ApineAutoload::load_kernel();
//header("Content-Type: text/plain");
//print_r(ApineAutoload::get_folder_files('lib'));
//$after = microtime(true) * 1000;
//die(number_format($after - $before, 1));

if (ApineConfig::get('runtime', 'mode') == 'development') {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
} else if (ApineConfig::get('runtime', 'mode') == 'production') {
	ini_set('display_errors', 'Off');
	error_reporting(E_ERROR);
}

date_default_timezone_set(ApineConfig::get('dateformat', 'timezone'));
ini_set('session.gc_maxlifetime', 604800);

/**
 * Main Execution
 */
try {
	// Make sure application runs with a valid execution mode
	if (ApineConfig::get('runtime', 'mode') != 'development' && ApineConfig::get('runtime', 'mode') != 'production') {
		throw new ApineException("Invalid Execution Mode \"".ApineConfig::get('runtime', 'mode')."\"", 418);
	}
	
	// Analyse and execute user request
	// This framework has to possible ways to handle user requests :
	//   - A RESTfull API
	//   - A regular Web Application
	/*if (Request::is_api_call()) {
		// TODO RESTful Implementation
		throw new ApineException("RESTful API call not implemented yet", 501);
	} else {*/
		if (!ApineRequest::is_api_call()) {
			$request = (isset(ApineRequest::get()['request'])) ? ApineRequest::get()['request'] : '/index';
		} else {
			$request = ApineRequest::get()['request'];
		}
		
		$route = ApineRouter::route($request);
		ApineRouter::execute($route->controller, $route->action, $route->args);
	//}
	
} catch (ApineException $e) {
	// Handle application errors
	$error = new ErrorController();
	
	if (ApineConfig::get('runtime', 'mode') != 'development'){
		if ($error_name = $error->method_for_code($e->getCode())) {
			$error->$error_name();
		} else {
			$error->server();
		}
	} else {
		$error->custom($e->getCode(), $e->getMessage(), $e);
	}
} catch (Exception $e) {
	$error = new ErrorController();
	$error->custom(500, $e->getMessage(), $e);
}