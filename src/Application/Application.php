<?php
/**
 * Main Application Execution
 * This script contains the representation of an APIne application
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Application;

use Apine\Core\Http\ServerRequest;
use Apine\Core\JsonStore;
use \Exception as Exception;
use Apine\Core\Request as Request;
use Apine\Core\Config as Config;
use Apine\Exception\GenericException as GenericException;
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
final class Application
{
    /**
     * Instance of the Application
     *
     * @var Application
     */
    private static $instance;
    
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
        "title"       => "APIne Framework",
        "authors"     => [
            "Tommy Teasdale"
        ],
        "description" => "",
        "version"     => "2.0.0-dev"
    ];
    
    /**
     * Application constructor.
     *
     * @return Application
     */
    public function __construct()
    {
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
    
        require_once 'vendor/autoload.php';
        
        if (!isset(self::$instance)) {
            self::$instance = &$this;
        }
        
        return self::$instance;
    }
    
    /**
     * @return Application
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     * Set the path to the application from the root for the virtual server
     * The application tries by default to guess it.
     *
     * @param string $a_webroot
     */
    public function setWebroot($a_webroot = '')
    {
        $this->webroot = $a_webroot;
    }
    
    /**
     * Run the application
     *
     * @param int $a_runtime Runtime mode
     */
    public function run()
    {
        //if (!strstr($this->apine_folder, 'vendor/youmy001')) {
        //require_once 'vendor/autoload.php';
        //}
        
        $headers = getallheaders();
        $isHttp = (isset($headers['HTTPS']) && !empty($headers['HTTPS']));
        $request = $_GET['apine-request'];
        $requestArray = explode("/", $request);
        $isAPICall = ($requestArray[1] === 'api');
        
        /**
         * Main Execution
         */
        try {
            // Verify is the protocol is allowed
            if (!$isHttp && !extension_loaded('xdebug')) {
                // Remove trailing slash
                $uri = rtrim($_GET['apine_request']);
                
                Routes::internalRedirect($uri, APINE_PROTOCOL_HTTPS)->draw();
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
                $gi = geoip_open($this->apine_folder . DIRECTORY_SEPARATOR . "Includes" . DIRECTORY_SEPARATOR . "GeoLiteCity.dat",
                    GEOIP_STANDARD);
                $record = GeoIP_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
                //$record = geoip_record_by_addr($gi, "24.230.215.89");
                //var_dump($record);
                
                if (isset($record)) {
                    $timezone = get_time_zone($record->country_code, ($record->region != '') ? $record->region : 0);
                } else {
                    if (!is_null($this->config->localization->defaults->timezone)) {
                        $timezone = $this->config->localization->defaults->timezone;
                    } else {
                        $timezone = 'America/New_York';
                    }
                }
                
                date_default_timezone_set($timezone);
            } else {
                if (!is_null($this->config->localization->defaults->timezone)) {
                    date_default_timezone_set($this->config->localization->defaults->timezone);
                }
            }
            
            //$request = Request::getRequestResource();
    
            if ($requestArray[1] === 'api') {
                $request = substr($request, 3);
            }
    
            if ((isset($this->config->use_api) && $this->config->use_api === true) && $isAPICall) {
                $router = new APIRouter();
            } else {
                if ($isAPICall) {
                    throw new GenericException('RESTful API calls are not implemented', 501);
                }
        
                if ((empty($request) || $request == '/')) {
                    $request = '/index';
                }
        
                $router = new WebRouter();
            }
            
            /*if ((isset($this->config->use_api) && $this->config->use_api === true) && Request::isApiCall()) {
                $router = new APIRouter();
            } else {
                if (Request::isApiCall()) {
                    throw new GenericException('RESTful API calls are not implemented', 501);
                }
                
                if ((empty($request) || $request == '/')) {
                    $request = '/index';
                }
                
                $router = new WebRouter();
            }*/
            
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
            if (!is_null($view) && is_a($view, 'Apine\MVC\View')) {
                $view->draw();
            } else {
                throw new GenericException('Empty Apine View', 488);
            }
        } catch (GenericException $e) {
            // Handle application errors
            try {
                $error = new Controllers\ErrorController();
                
                if (!$this->debug_mode) {
                    if ($error_name = $error->methodForCode($e->getCode())) {
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
                $protocol = (isset($headers['SERVER_PROTOCOL']) ? $headers['SERVER_PROTOCOL'] : 'HTTP/1.0');
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
                $protocol = (isset($headers['SERVER_PROTOCOL']) ? $headers['SERVER_PROTOCOL'] : 'HTTP/1.0');
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
    public function isDebugMode()
    {
        return $this->debug_mode;
    }
    
    /**
     * Return the system configuration handler
     *
     * @return Config
     */
    public function getConfig()
    {
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
    public function getWebroot()
    {
        return $this->webroot;
    }
    
    /**
     * Return the version
     *
     * @return Version
     */
    public function getVersion()
    {
        if (is_null($this->version)) {
            $this->version = new Version($this->apine_version, $this->application['version']);
        }
        
        return $this->version;
    }
    
    public function getName()
    {
        return $this->application['title'];
    }
    
    public function getDescription()
    {
        return $this->application['description'];
    }
    
    public function getAuthors()
    {
        return implode(', ', $this->application['authors']);
    }
    
    /**
     * Return the location of the framework on the server
     *
     * @return string
     */
    public function frameworkLocation()
    {
        return $this->apine_folder;
    }
    
    /**
     * Return the default include path of the application
     *
     * @return string
     */
    public function includePath()
    {
        return $this->include_path;
    }
}