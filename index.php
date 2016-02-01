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

//require_once('vendor/autoload.php');
require_once('lib/core/autoloader.php');
ini_set('display_errors', -1);
ini_set('include_path', realpath(dirname(__FILE__)));
ApineAutoload::load_kernel();

// Sets how the framework manages errors and execptions
if (ApineConfig::get('runtime', 'mode') == 'development') {
	ini_set('display_errors', -1);
	error_reporting(E_ALL | E_STRICT);
} else if (ApineConfig::get('runtime', 'mode') == 'production') {
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}

if (ApineConfig::get('runtime', 'use_composer') === "yes") {
	require_once('vendor/autoload.php');
}

// Find a timezone for the user
// using geoip library and its local database
if (function_exists('geoip_open')) {
	$gi = geoip_open("lib/GeoLiteCity.dat", GEOIP_STANDARD);
	$record = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
	//$record = geoip_record_by_addr($gi, "24.230.215.89");
	
	if (isset($record)) {
		date_default_timezone_set(get_time_zone($record->country_code, ($record->region!='') ? $record->region : 0));
	} else if (!is_null(ApineConfig::get('dateformat', 'timezone'))) {
		date_default_timezone_set(ApineConfig::get('dateformat', 'timezone'));
	}
} else if (!is_null(ApineConfig::get('dateformat', 'timezone'))) {
	date_default_timezone_set(ApineConfig::get('dateformat', 'timezone'));	
}

/**
 * Main Execution
 */
try {
	// Make sure application runs with a valid execution mode
	if (ApineConfig::get('runtime', 'mode') != 'development' && ApineConfig::get('runtime', 'mode') != 'production') {
		throw new ApineException('Invalid Execution Mode \"'.ApineConfig::get('runtime', 'mode').'"', 418);
	}
	
	// Verify is the protocol is allowed
	if (ApineRequest::is_https() && ApineConfig::get('runtime', 'use_https') == 'no') {
		internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
	}
	
	if (ApineConfig::get('runtime', 'route_format') == 'json') {
		if (file_exists('routes.xml')) {
			if (!file_exists('routes.json')) {
				file_put_contents('routes.json', json_encode(export_routes('routes.xml'), JSON_PRETTY_PRINT));
			}
		} else if (!file_exists('routes.xml') && !file_exists('routes.json')) {
			throw new ApineException('Route File Not Found', 418);
		}
	} else if (is_null(ApineConfig::get('runtime', 'route_format'))) {
		if (file_exists('routes.xml')) {
			if (!file_exists('routes.json')) {
				file_put_contents('routes.json', json_encode(export_routes('routes.xml'), JSON_PRETTY_PRINT));
			}
			ApineConfig::set('runtime', 'route_format', 'json');
		} else if (!file_exists('routes.xml') && !file_exists('routes.json')) {
			throw new ApineException('Route File Not Found', 418);
		}
	} else if (ApineConfig::get('runtime', 'route_format') != 'xml') {
		throw new ApineException('Route Format Invalid', 418);
	}
	
	// If a user is logged in; redirect to the allowed protocol
	// Secure session only work when Use HTTPS is set to "yes"
	if (ApineSession::is_logged_in()) {
		if (ApineConfig::get('runtime', 'secure_session') == 'yes') {
			if (!ApineRequest::is_https() && ApineConfig::get('runtime', 'use_https') == 'yes') {
				internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTPS);
			} else if (ApineRequest::is_https() && ApineConfig::get('runtime', 'use_https') == 'no') {
				internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
			}
		} else {
			if (ApineRequest::is_https()) {
				internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
			}
		}
	}
	
	// Analyse and execute user request
	// This framework has to possible ways to handle user requests :
	//   - A RESTfull API
	//   - A regular Web Application
	if (!ApineRequest::is_api_call()) {
		if (!empty(ApineRequest::get()['request']) && ApineRequest::get()['request'] != '/') {
			$request = ApineRequest::get()['request'];
		} else {
			$request = '/index';
		}
	} else {
		if (ApineConfig::get('runtime', 'use_api') == 'yes') {
			$request = ApineRequest::get()['request'];
		} else {
			throw new ApineException('RESTful API call is not implemented', 501);
		}
	}
	
	// Fetch and execute the route
	$route = ApineRouter::route($request);
	$view = ApineRouter::execute($route->controller, $route->action, $route->args);
	
	// Draw the output is a view is returned
	if(!is_null($view) && is_object($view) && get_parent_class($view) == 'ApineView') {
		$view->draw();
	}
} catch (ApineException $e) {
	// Handle application errors
	$error = new ErrorController();
	
	if (ApineConfig::get('runtime', 'mode') != 'development'){
		if ($error_name = $error->method_for_code($e->getCode())) {
			$view = $error->$error_name();
		} else {
			$view = $error->server();
		}
	} else {
		$view = $error->custom($e->getCode(), $e->getMessage(), $e);
	}
	
	$view->draw();
} catch (Exception $e) {
	// Handle PHP exceptions
	$error = new ErrorController();
	$view = $error->custom(500, $e->getMessage(), $e);
	$view->draw();
}