<?php
/**
 * Main Application Execution
 * This script contains the representation of an APIne application
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Application;

use \Exception as Exception;
use Apine\Core\Request as Request;
use Apine\Core\Config as Config;
use Apine\Exception\GenericException as GenericException;
use Apine\Session\SessionManager as SessionManager;
use Apine\Routing\WebRouter as WebRouter;
use Apine\Routing\APIRouter as APIRouter;
use Apine\Controllers\System as Controllers;
use Apine\Core\Version as Version;
use Apine\Utility\Routes;

/**
 * Apine Application
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Application
 */
final class Application {
	
	/**
	 * Instance of the Application
	 * 
	 * @var Application
	 */
	private static $_instance;
	
	/**
	 * Version number of the framework
	 * 
	 * @var string
	 */
	private $apine_version = '2.0.0-dev';
	
	/**
	 * Version number of the user application
	 * 
	 * @var string
	 */
	private $application_version; 
	
	/**
	 * Name of the folder where the framework is located
	 * 
	 * @var string
	 */
	private $apine_folder;
	
	private $include_path;
	
	/**
	 * Can the framework use Composer and offer advanced features
	 * 
	 * @var bool $use_composer
	 */
	private $use_composer = true;
	
	/**
	 * Execution mode
	 * 
	 * @var integer
	 */
	private $mode = APINE_MODE_PRODUCTION;
	
	/**
	 * Is the application allowed to use HTTPS connection
	 * 
	 * @var bool
	 */
	private $use_https = false;
	
	/**
	 * Path to route file
	 * 
	 * @var string
	 */
	private $routes_path = 'routes.json';
	
	/**
	 * Should Session transactions be secured through HTTPS connection
	 * 
	 * @var bool
	 */
	private $secure_session = true;
	
	/**
	 * Path to the APIne Application from the webroot
	 * 
	 * @var string $webroot
	 */
	private $webroot = '';
	
	/**
	 * APIne Application Config
	 * 
	 * @var Config
	 */
	private $config;
	
	/**
	 * APIne versions
	 * 
	 * @var Version
	 */
	private $version;

    /**
     * Application constructor.
     *
     * @return Application
     */
    public function __construct() {
		
		ini_set('display_errors', 0);
		error_reporting(E_ERROR);
		
		$server_root = $_SERVER['DOCUMENT_ROOT'];
		$this->apine_folder = realpath(dirname(__FILE__) . '/..'); // The path to the framework itself
        
       // Compute if in a sub directory
		if (strlen($_SERVER['SCRIPT_NAME']) > 10) {
			// Remove "/index.php" from the script name
			$this->webroot = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
		}
		
		// The include path should the the server root plus the webrout path
		$include_path = realpath(implode('/', array($server_root, $this->webroot)));
		
		if (!is_dir($include_path)) {
			$include_path = realpath(dirname($include_path));
		}
		
		$this->include_path = $include_path;
		
		ini_set('include_path', $include_path);
		
		if (!isset(self::$_instance)) {
			self::$_instance = &$this;
		}

        return self::$_instance;
		
	}
	
	/**
	 * @return Application
	 */
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

    /**
     * Define if the application must force the use of https for use sessions
     *
     * @param bool $a_bool
     */
	public function set_secure_session ($a_bool = true) {
		
		if (is_bool($a_bool)) {
			$this->secure_session = $a_bool;
		}
		
	}

    /**
     * Set the version number of the application
     *
     * @param $a_version_number
     * @throws Exception
     */
    public function set_application_version ($a_version_number) {
		
		if (Version::validate($a_version_number)) {
			$this->application_version = $a_version_number;
		} else {
			throw new Exception('Invalid Version number');
		}
		
	}
	
	/**
     * Set the path to the application from the root for the virtual server
     * 
     * The application tries by default to guess it.
     *
     * @param string $a_webroot
     */
    public function set_webroot ($a_webroot = '') {
        
        $this->webroot = $a_webroot;
        
    }

    /**
     * Set the error reporting mode of the application
     *
     * @param int $a_mode
     */
	public function set_mode ($a_mode = APINE_MODE_PRODUCTION) {
		
		if ($a_mode !== $this->mode) {
			$this->mode = $a_mode;
			if ($a_mode === APINE_MODE_DEVELOPMENT) {
				//ini_set('display_errors', -1);
				ini_set('display_errors', 1);
				error_reporting(E_ALL | E_STRICT | E_DEPRECATED | E_WARNING);
			} else {
				//ini_set('display_errors', 0);
				ini_set('display_errors', 0);
				error_reporting(E_ERROR);
			}
		}
		
	}

    /**
     * Run the application
     *
     * @param int $a_runtime Runtime mode
     */
	public function run ($a_runtime = APINE_RUNTIME_HYBRID) {
		
		if ($a_runtime !== APINE_RUNTIME_HYBRID && $a_runtime !== APINE_RUNTIME_API && $a_runtime !== APINE_RUNTIME_APP) {
			$a_runtime = APINE_RUNTIME_HYBRID;
		}
		
		if (!strstr($this->apine_folder, 'vendor/youmy001')) {
			require_once 'vendor/autoload.php';
		}
		
		/**
		 * Main Execution
		 */
		try {
			// Make sure application runs with a valid execution mode
			if ($this->mode !== APINE_MODE_DEVELOPMENT && $this->mode !== APINE_MODE_PRODUCTION) {
				throw new GenericException('Invalid Execution Mode \"' . $this->mode . '"', 418);
			}
			
			if (!file_exists('.htaccess') || !file_exists('config.ini')) {
				$protocol = (isset(Request::server()['SERVER_PROTOCOL']) ? Request::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
				header($protocol . ' 503 Service Unavailable');
				die("Critical Error : Framework Installation Not Completed");
			}
			
			if (!Request::is_api_call() && (!empty(Request::get()['request']) && Request::get()['request'] != '/')) {
				$request = Request::get()['request'];
			} else if (!Request::is_api_call()) {
				$request = '/index';
			} else {
				$request = Request::get()['request'];
			}
			
			// Verify is the protocol is allowed
			if (Request::is_https() && !$this->use_https) {
				Routes::internal_redirect($request, APINE_PROTOCOL_HTTP);
			}
			
			if (is_null($this->config)) {
				$this->config = new Config('config.ini');
			}
			
			// If a user is logged in; redirect to the allowed protocol
			// Secure session only work when Use HTTPS is set to "yes"
			if (SessionManager::is_logged_in()) {
				if ($this->secure_session) {
					if (!Request::is_https() && $this->use_https) {
						Routes::internal_redirect($request, APINE_PROTOCOL_HTTPS)->draw();
						die();
					} else if (Request::is_https() && !$this->use_https) {
						Routes::internal_redirect($request, APINE_PROTOCOL_HTTP)->draw();
						die();
					}
				} else {
					if (Request::is_https()) {
						Routes::internal_redirect($request, APINE_PROTOCOL_HTTP)->draw();
						die();
					}
				}
			}

			unset($request);
			
			// Find a timezone for the user
			// using geoip library and its local database
			if (function_exists('geoip_open')) {
				$gi = geoip_open($this->apine_folder . DIRECTORY_SEPARATOR . "Includes" . DIRECTORY_SEPARATOR . "GeoLiteCity.dat", GEOIP_STANDARD);
				$record = GeoIP_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
				//$record = geoip_record_by_addr($gi, "24.230.215.89");
				//var_dump($record);
			
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
			
			if (!Request::is_api_call()) {
				if ($a_runtime == APINE_RUNTIME_API) {
					throw new GenericException('Web Application calls are not implemented', 501);
				}
				
				if (!empty(Request::get()['request']) && Request::get()['request'] != '/') {
					$request = Request::get()['request'];
				} else {
					$request = '/index';
				}
				
				$router = new WebRouter($this->routes_path);
			} else {
				if ($a_runtime == APINE_RUNTIME_APP) {
					throw new GenericException('RESTful API calls are not implemented', 501);
				}
				
				$request = Request::get()['request'];
				$router = new APIRouter();
			}
			
			// Fetch and execute the route
			$route = $router->route($request);
			$view = $router->execute($route->controller, $route->action, $route->args);
			
			// Draw the output is a view is returned
			if(!is_null($view) && is_a($view, 'Apine\MVC\View')) {
				$view->draw();
			} else {
				throw new GenericException('Empty Apine View', 488);
			}
		} catch (GenericException $e) {
			// Handle application errors
			try {
				$error = new Controllers\ErrorController();
				
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
				var_dump($e2->getTraceAsString());
				$protocol = (isset(Request::server()['SERVER_PROTOCOL']) ? Request::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
				header($protocol . ' 500 Internal Server Error');
				die("Critical Error : " . $e->getMessage());
			}
		} catch (Exception $e) {
			// Handle PHP exceptions
			try {
				$error = new Controllers\ErrorController();
				$view = $error->custom(500, $e->getMessage(), $e);
				$view->draw();
			} catch (Exception $e2) {
				$protocol = (isset(Request::server()['SERVER_PROTOCOL']) ? Request::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
				header($protocol . ' 500 Internal Server Error');
				die("Critical Error : " . $e->getMessage());
			}
		}
		
	}

    /**
     * Return the current mode
     *
     * @return int
     */
	public function get_mode () {
		
		return $this->mode;
		
	}

    /**
     * Return the https state
     *
     * @return bool
     */
	public function get_use_https () {
		
		return (bool) $this->use_https;
		
	}

    /**
     * Return the secure session state
     *
     * @return bool
     */
	public function get_secure_session () {
		
		return (bool) $this->secure_session;
		
	}

    /**
     * Return the system configuration handler
     *
     * @return Config
     */
	public function get_config () {
		
		return $this->config;
		
	}

    /**
     * Return the path of the routes file
     *
     * @return string
     */
	public function get_routes_path () {
		
		return $this->routes_path;
	}

    /**
     * Return the path to the root of the host
     *
     * @return string
     */
	public function get_webroot () {
		
		return $this->webroot;
		
	}

    /**
     * Return the version
     *
     * @return Version
     */
	public function get_version () {
		
		if (is_null($this->version)) {
			$this->version = new Version($this->apine_version, $this->application_version);
		}
		
		return $this->version;
		
	}

    /**
     * Return the location of the framework on the server
     *
     * @return string
     */
	public function framework_location () {
		
		return $this->apine_folder;
		
	}

    /**
     * Return the default include path of the application
     *
     * @return string
     */
	public function include_path () {
	
		return $this->include_path;
	
	}
	
}
