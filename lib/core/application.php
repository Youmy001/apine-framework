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
	
	private static $_instance;
	
	private $version = '1.0.0-dev.15.01';
	
	private $apine_folder;
	
	private $use_composer = true;
	
	private $mode = APINE_MODE_PRODUCTION;
	
	private $use_https = false;
	
	private $routes_path = 'routes.json';
	
	private $routes_type = APINE_ROUTES_JSON;
	
	private $secure_session = true;
	
	private $webroot = '';
	
	private $config;
	
	public function __construct() {
		
		$this->apine_folder = realpath(dirname(__FILE__) . '/..');
			
		ini_set('display_errors', 0);
		error_reporting(E_ERROR);
		ini_set('include_path', realpath($this->apine_folder . '/..'));
		
		if (!isset(self::$_instance)) {
			self::$_instance = &$this;
		} else {
			return self::$_instance;
		}
		
	}
	
	public static function get_instance () {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	/**
	 * Set if the application allowed to use https
	 * 
	 * @param bool $a_bool
	 */
	public function set_use_https ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			$this->use_https = $a_bool;
		}
		
	}
	
	public function set_secure_session ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			$this->secure_session = $a_bool;
		}
		
	}
	
	public function use_composer ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			$this->use_composer = $a_bool;
		}
		
	}
	
	public function load_config ($a_path) {
		
		if (file_exists($a_path)) {
			$this->config = new ApineConfig($a_path);
		}
		
	}
	
	public function load_routes ($a_path) {
		
	}
	
	public function set_route_type ($a_type = APINE_ROUTES_JSON) {
		
		if ($a_type === APINE_ROUTES_JSON || $a_type === APINE_ROUTES_XML) {
			$this->routes_type = $a_type;
		} else {
			$this->routes_type = APINE_ROUTES_JSON;
		}
		
	}
	
	public function set_mode ($a_mode = APINE_MODE_PRODUCTION) {
		
		if ($a_mode !== $this->mode) {
			$this->mode = $a_mode;
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
			if ($this->mode !== APINE_MODE_DEVELOPMENT && $this->mode !== APINE_MODE_PRODUCTION) {
				throw new ApineException('Invalid Execution Mode \"' . $this->mode . '"', 418);
			}
			
			// Verify is the protocol is allowed
			if (ApineRequest::is_https() && !$this->use_https) {
				internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTP);
			}
			
			if (is_null($this->config)) {
				$this->config = new ApineConfig('config.ini');
			}
			
			// If a user is logged in; redirect to the allowed protocol
			// Secure session only work when Use HTTPS is set to "yes"
			if (ApineSession::is_logged_in()) {
				if ($this->secure_session) {
					if (!ApineRequest::is_https() && $this->use_https) {
						internal_redirect(ApineRequest::get()['request'], APINE_PROTOCOL_HTTPS);
					} else if (ApineRequest::is_https() && !$this->use_https) {
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
					$timezone = get_time_zone($record->country_code, ($record->region!='') ? $record->region : 0);
				} else if (!is_null($this->config->get('dateformat', 'timezone'))) {
					$timezone = $this->config->get('dateformat', 'timezone');
				} else {
					$timezone = 'America/New_York';
				}
				
				date_default_timezone_set($timezone);
			} else if (!is_null($this->config->get('dateformat', 'timezone'))) {
				date_default_timezone_set($this->config->get('dateformat', 'timezone'));
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
				
				$router = new ApineWebRouter($this->routes_path, $this->routes_type);
			} else {
				if ($a_runtime == APINE_RUNTIME_APP) {
					throw new ApineException('RESTful API calls are not implemented', 501);
				}
				
				$request = ApineRequest::get()['request'];
				$router = new ApineAPIRouter();
			}
			
			// Fetch and execute the route
			//$router = new ApineRouter($this->routes_path, $this->routes_type);
			$route = $router->route($request);
			$view = $router->execute($route->controller, $route->action, $route->args);
			
			// Draw the output is a view is returned
			if(!is_null($view) && is_a($view, 'ApineView')) {
				$view->draw();
			}
		} catch (ApineException $e) {
			// Handle application errors
			try {
				$error = new ErrorController();
				
				if ($this->mode == APINE_MODE_PRODUCTION){
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
	
	public function get_mode () {
		
		return $this->mode;
		
	}
	
	public function get_use_https () {
		
		return (bool) $this->use_https;
		
	}
	
	public function get_secure_session () {
		
		return (bool) $this->secure_session;
		
	}
	
	public function get_config () {
		
		return $this->config;
		
	}
	
	public function get_routes_path () {
		
		return $this->routes_path;
	}
	
	public function get_routes_type () {
		
		return  $this->routes_type;
		
	}
	
	public function get_webroot () {
		
		return $this->webroot;
		
	}
	
	public function get_version () {
		
		return $this->version;
		
	}
	
}