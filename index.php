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
Autoload::load_kernel();
ini_set('include_path', realpath(dirname(__FILE__)));

if (Config::get('runtime', 'mode') == 'development') {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
} else if (Config::get('runtime', 'mode') == 'production') {
	ini_set('display_errors', 'Off');
	error_reporting(E_ERROR);
}

date_default_timezone_set(Config::get('dateformat', 'timezone'));
ini_set('session.gc_maxlifetime', 604800);

/**
 * Main Execution
 */
try {
	// Make sure application runs with a valid execution mode
	if (Config::get('runtime', 'mode') != 'development' && Config::get('runtime', 'mode') != 'production') {
		throw new ApineException("Invalid Execution Mode \"".Config::get('runtime', 'mode')."\"");
	}
	
	// Analyse and execute user request
	// This framework has to possible ways to handle user requests :
	//   - A RESTfull API
	//   - A regular Web Application
	if (Request::is_api_call()) {
		// TODO RESTful Implementation
		throw new ApineException("RESTful API call not implemented yet", 501);
	} else {
		if (!Request::is_api_call()) {
			$request = (isset(Request::get()['request'])) ? Request::get()['request'] : '/index';
		} else {
			$request = Request::get()['request'];
		}
		
		$route = ApineRouter::route($request);
		ApineRouter::execute($route->controller, $route->action, $route->args);
	}
	
} catch (ApineException $e) {
	// Handle application errors
	$error = new ErrorController();
	
	if (Config::get('runtime', 'mode') != 'development'){
		if ($error_name = $error->method_for_code($e->getCode())) {
			$error->$error_name();
		} else {
			$error->server();
		}
	} else {
		$error->custom($e->getCode(), $e->getMessage(), $e);
	}
} catch (Exception $e) {
	print $e->getMessage().' on '.$e->getFile().' ('.$e->getLine().")\n\n";
}