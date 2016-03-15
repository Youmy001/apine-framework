<?php
/**
 * Main Application Execution
 * This script contains the representation of an APIne application
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * #@+
 * Constants
 */
define('APINE_MODE_DEVELOPMENT', 5);
define('APINE_MODE_PRODUCTION', 6);
define('APINE_RUNTIME_API', 16);
define('APINE_RUNTIME_APP', 17);
define('APINE_RUNTIME_HYBRID', 18);
define('APINE_ROUTES_JSON', 25);
define('APINE_ROUTES_XML', 26);

final class ApineApplication {
	
	private static $started = false;
	
	private $apine_folder;
	
	private $use_composer = true;
	
	private static $mode = APINE_MODE_PRODUCTION;
	
	private static $use_https = false;
	
	private static $config_path = 'config.ini';
	
	private static $routes_path = 'routes.json';
	
	private static $route_type = APINE_ROUTES_JSON;
	
	private static $secure_session = true;
	
	private static $before;
	
	public function __construct() {
		
		self::$before = microtime(true) * 1000;
		$this->apine_folder = realpath(dirname(__FILE__) . '/..');
		
		ini_set('display_errors', 0);
		error_reporting(E_ERROR);
		ini_set('include_path', realpath($this->apine_folder . '/..'));
		
		self::$started = true;
		
	}
	
	/**
	 * Set if the application allowed to use https
	 * 
	 * @param bool $a_bool
	 */
	public function set_use_https ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			self::$use_https = $a_bool;
		}
		
	}
	
	public function set_secure_session ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			self::$secure_session = $a_bool;
		}
		
	}
	
	public function use_composer ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			$this->use_composer = $a_bool;
		}
		
	}
	
	public function load_config ($a_path) {
		
	}
	
	public function load_routes ($a_path) {
		
	}
	
	public function set_route_type ($a_type = APINE_ROUTES_JSON) {
		
		if ($a_type === APINE_ROUTES_JSON || $a_type === APINE_ROUTES_XML) {
			$this->route_type = $a_type;
		} else {
			$this->route_type = APINE_ROUTES_JSON;
		}
		
	}
	
	public function set_mode ($a_mode = APINE_MODE_PRODUCTION) {
		
		if ($a_mode !== self::$mode) {
			self::$mode = $a_mode;
			if ($a_mode === APINE_MODE_DEVELOPMENT) {
				ini_set('display_errors', -1);
				error_reporting(E_ALL | E_STRICT);
			} else {
				ini_set('display_errors', 0);
				error_reporting(E_ERROR);
			}
		}
		
	}
	
	public function run ($a_runtime = APINE_RUNTIME_HYBRID) {
		
		if ($a_runtime !== APINE_RUNTIME_HYBRID && $a_runtime !== APINE_RUNTIME_API && $a_runtime !== APINE_RUNTIME_APP) {
			$a_runtime = APINE_RUNTIME_HYBRID;
		}
		
		if ($this->use_composer) {
			require_once('vendor/autoload.php');
		}
		
		/**
		 * Main Execution
		 */
		try {
			// Make sure application runs with a valid execution mode
			if (self::$mode !== APINE_MODE_DEVELOPMENT && self::$mode !== APINE_MODE_PRODUCTION) {
				throw new ApineException('Invalid Execution Mode \"' . self::$mode . '"', 418);
			}
			
			// Verify is the protocol is allowed
			if (ApineRequest::is_https() && !self::$use_https) {
				internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
			}
			
			// Verify if the route file exists
			if ($a_runtime !== APINE_RUNTIME_API) {
				if (!file_exists(self::$routes_path)) {
					if (self::$route_type == APINE_ROUTES_JSON && file_exists('routes.xml')) {
						file_put_contents(self::$routes_path, json_encode(export_routes('routes.xml'), JSON_PRETTY_PRINT));
					}
					throw new ApineException('Route File Not Found', 418);
				}
			}
			
			// If a user is logged in; redirect to the allowed protocol
			// Secure session only work when Use HTTPS is set to "yes"
			if (ApineSession::is_logged_in()) {
				if (self::$secure_session) {
					if (!ApineRequest::is_https() && self::$use_https) {
						internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTPS);
					} else if (ApineRequest::is_https() && !self::$use_https) {
						internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
					}
				} else {
					if (ApineRequest::is_https()) {
						internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
					}
				}
			}
			
			// Find a timezone for the user
			// using geoip library and its local database
			if (function_exists('geoip_open')) {
				$gi = geoip_open($this->apine_folder . "/GeoLiteCity.dat", GEOIP_STANDARD);
				$record = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
				//$record = geoip_record_by_addr($gi, "24.230.215.89");
			
				if (isset($record)) {
					date_default_timezone_set(get_time_zone($record->country_code, ($record->region!='') ? $record->region : 0));
				} else if (!is_null(ApineAppConfig::get('dateformat', 'timezone'))) {
					date_default_timezone_set(ApineAppConfig::get('dateformat', 'timezone'));
				}
			} else if (!is_null(ApineAppConfig::get('dateformat', 'timezone'))) {
				date_default_timezone_set(ApineAppConfig::get('dateformat', 'timezone'));
			}
			
			if (!ApineRequest::is_api_call()) {
				if ($a_runtime == APINE_RUNTIME_API) {
					throw new ApineException('Web Application calls are not implemented', 501);
				}
				
				if (!empty(ApineRequest::get()['request']) && ApineRequest::get()['request'] != '/') {
					$request = ApineRequest::get()['request'];
				} else {
					$request = '/index';
				}
			} else {
				if ($a_runtime == APINE_RUNTIME_APP) {
					throw new ApineException('RESTful API calls are not implemented', 501);
				}
				
				$request = ApineRequest::get()['request'];
			}
			
			// Fetch and execute the route
			$route = ApineRouter::route($request);
			$view = ApineRouter::execute($route->controller, $route->action, $route->args);
			
			// Draw the output is a view is returned
			if(!is_null($view) && is_a($view, 'ApineView')) {
				$view->draw();
			}
		} catch (ApineException $e) {
			// Handle application errors
			try {
				$error = new ErrorController();
				
				if (self::$mode == APINE_MODE_PRODUCTION){
					if ($error_name = $error->method_for_code($e->getCode())) {
						$view = $error->$error_name();
					} else {
						$view = $error->server();
					}
				} else {
					$view = $error->custom($e->getCode(), $e->getMessage(), $e);
				}
				
				$view->draw();
			} catch (Exception $e2) {
				$protocol = (isset(ApineRequest::server()['SERVER_PROTOCOL']) ? ApineRequest::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
				header($protocol . ' 500 Internal Server Error');
				die("Critical Error : " . $e->getMessage());
			}
		} catch (Exception $e) {
			// Handle PHP exceptions
			try {
				$error = new ErrorController();
				$view = $error->custom(500, $e->getMessage(), $e);
				$view->draw();
			} catch (Exception $e2) {
				$protocol = (isset(ApineRequest::server()['SERVER_PROTOCOL']) ? ApineRequest::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
				header($protocol . ' 500 Internal Server Error');
				die("Critical Error : " . $e->getMessage());
			}
		}
		
	}
	
	public static function mode () {
		
		return self::$mode;
		
	}
	
	public static function use_https () {
		
		return (bool) self::$use_https;
		
	}
	
	public static function secure_session () {
		
		return (bool) self::$secure_session;
		
	}
	
	public static function config_path () {
		
		return self::$config_path;
		
	}
	
	public static function route_type () {
		
		return self::$route_type;
		
	}
	
	public static function routes_path () {
		
		return self::$routes_path;
		
	}
	
	public static function execution_time () {
		
		if (self::$started) {
			$before = self::$before;
			
			$after = microtime(true) * 1000;
			return number_format($after - $before, 1);
		} else {
			return false;
		}
		
	}
	
}