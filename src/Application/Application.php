<?php
/**
 * Main Application Execution
 * This script contains the representation of an APIne application
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Application;

use Apine\Core\JsonStore;
use Apine\Session\SessionData;
use Apine\Translation\TranslationDirectory;
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
	//private $application_version;
	
	/**
	 * Name of the folder where the framework is located
	 * 
	 * @var string
	 */
	private $apine_folder;
	
	private $include_path;
	
	/**
	 * Debug mode
	 * 
	 * @var boolean
	 */
	private $debug_mode = false;
	
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
	
	private $application = [
		"title" => "APIne Framework",
		"authors" => [
			"Tommy Teasdale"
		],
		"description" => "",
		"version" => "2.0.0-dev"
	];

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
		/*if (strlen($_SERVER['SCRIPT_NAME']) > 10) {
			// Remove "/index.php" from the script name
			$this->webroot = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
		}*/
		if ((strlen($_SERVER['SCRIPT_NAME']) - strlen($server_root)) > 10) {
			// Remove "/index.php" from the script name
			$this->webroot = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
		}
		
		// The include path should the the server root plus the webroot path
		$include_path = realpath(implode('/', array($server_root, $this->webroot)));
		
		if (!is_dir($include_path)) {
			$include_path = realpath(dirname($include_path));
		}
		
		$this->include_path = $include_path;
		
		ini_set('include_path', $include_path);
		chdir($include_path);
		
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
     * Run the application
     *
     * @param int $a_runtime Runtime mode
     */
	public function run () {
		
		//if (!strstr($this->apine_folder, 'vendor/youmy001')) {
			require_once 'vendor/autoload.php';
		//}
		
		/**
		 * Main Execution
		 */
		try {
			// Verify is the protocol is allowed
			if (!Request::is_https() && !extension_loaded('xdebug')) {
				Routes::internal_redirect(Request::get_request_resource(), APINE_PROTOCOL_HTTPS)->draw();
				die();
			}
			
			// Verify if the minimum file dependencies are fulfilled
			if (!file_exists('.htaccess') || !file_exists('settings.json')) {
				$this->debug_mode = true;
				ini_set('display_errors', 1);
				error_reporting(E_ALL | E_STRICT | E_DEPRECATED | E_WARNING);
				throw new GenericException('Framework Installation Not Completed', 503);
			}
			
			$settings = JsonStore::get('settings.json');
			$this->application = (!empty($settings->application)) ? (array)$settings->application : $this->application;
			
			if (is_null($this->config)) {
				$this->config = new Config('settings.json');
			}
			
			// Make sure application runs with a valid execution mode
			if ($this->config->debug !== null) {
				$bool = $this->config->debug;
				if (is_bool($bool)) {
					$this->debug_mode = $bool;
				}
			}
			
			if ($this->debug_mode === true) {
				ini_set('display_errors', 1);
				error_reporting(E_ALL | E_STRICT | E_DEPRECATED | E_WARNING);
			}
			
			// Find a timezone for the user
			// using geoip library and its local database
			if (function_exists('geoip_open')) {
				$gi = geoip_open($this->apine_folder . DIRECTORY_SEPARATOR . "Includes" . DIRECTORY_SEPARATOR . "GeoLiteCity.dat", GEOIP_STANDARD);
				$record = GeoIP_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
				//$record = geoip_record_by_addr($gi, "24.230.215.89");
				//var_dump($record);
				
				if (isset($record)) {
					$timezone = get_time_zone($record->country_code, ($record->region!='') ? $record->region : 0);
				} else if (!is_null($this->config->localization->defaults->timezone)) {
					$timezone = $this->config->localization->defaults->timezone;
				} else {
					$timezone = 'America/New_York';
				}
				
				date_default_timezone_set($timezone);
			} else if (!is_null($this->config->localization->defaults->timezone)) {
				date_default_timezone_set($this->config->localization->defaults->timezone);
			}
			
			$request = Request::get_request_resource();
			
			if ((isset($this->config->use_api) && $this->config->use_api === true) && Request::is_api_call()) {
				$router = new APIRouter();
			} else {
				if (Request::is_api_call()) {
					throw new GenericException('RESTful API calls are not implemented', 501);
				}
				
				if ((empty($request) || $request == '/')) {
					$request = '/index';
				}
				
				$router = new WebRouter();
			}
			
			/*if (!Request::is_api_call()) {
				if ($a_runtime == APINE_RUNTIME_API) {
					throw new GenericException('Web Application calls are not implemented', 501);
				}
				
				if ((empty($request) || $request == '/')) {
					$request = '/index';
				}
				
				$router = new WebRouter();
			} else {
				if ((isset($this->config->use_api) && $this->config->use_api === false) || (!isset($this->config->use_api))) {
					throw new GenericException('RESTful API calls are not implemented', 501);
				}
				
				$router = new APIRouter();
			}*/
			
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
				
				if (!$this->debug_mode){
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
	public function is_debug_mode () {
		
		return $this->debug_mode;
		
	}

    /**
     * Return the system configuration handler
     *
     * @return Config
     */
	public function get_config () {
		
		if (is_null($this->config)) {
			$this->config = new Config('settings.json');
		}
		
		return $this->config;
		
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
			$this->version = new Version($this->apine_version, $this->application['version']);
		}
		
		return $this->version;
		
	}
	
	public function get_name () {
		return $this->application['title'];
	}
	
	public function get_description () {
		return $this->application['description'];
	}
	
	public function get_authors () {
		return implode(', ', $this->application['authors']);
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