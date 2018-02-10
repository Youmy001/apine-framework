<?php
/**
 * Main Application Execution
 * This script contains the representation of an APIne application
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Application;

use Apine\Core\Container\Container;
use Apine\Core\Database as BasicDatabase;
use Apine\Core\Database\Database;
use Apine\Core\Database\Connection;
use Apine\Core\JsonStore;
use Apine\Core\Routing\ResourcesContainer;
use Apine\Core\Routing\Router;
use Apine\Core\Request;
use Apine\Core\Config;
use Apine\Exception\GenericException;
use Apine\Controllers\System as Controllers;
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
    private $debug = false;
    
    /**
     * Path to the APIne Application from the webroot
     *
     * @var string $webroot
     */
    private $webroot = '';
    
    /**
     * @var ServiceProvider
     */
    private $serviceProvider;
    
    /**
     * @var Container
     */
    private $apiResources;
    
    /**
     * Application constructor.
     */
    public function __construct()
    {
    
        try {
            ini_set('display_errors', '0');
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
    
            // Verify if the minimum file dependencies are fulfilled
            if (!file_exists('.htaccess') || !file_exists('settings.json')) {
                $this->debug = true;
                ini_set('display_errors', '1');
                error_reporting(E_ALL | E_STRICT | E_DEPRECATED | E_WARNING);
                throw new GenericException('Framework Installation Not Completed', 503);
            }
    
            if (!strstr($this->apine_folder, 'vendor/youmy001')) {
                require_once 'vendor/autoload.php';
            }
    
            $this->serviceProvider = ServiceProvider::getInstance();
            //$this->apiResources = new Container();
            $this->apiResources = new ResourcesContainer();
        } catch (GenericException $e) {
            $headers = getallheaders();
            $protocol = (isset($headers['SERVER_PROTOCOL']) ? $headers['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $e->getCode() . ' Internal Server Error');
            die("Critical Error : " . $e->getMessage());
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        
        /*if (!isset(self::$instance)) {
            self::$instance = &$this;
        }
        
        return self::$instance;*/
    }
    
    /**
     * @return Application
     */
    /*public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }*/
    
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
        $config = null;
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
            
            $config = new Config('settings.json');
            
            // Make sure application runs with a valid execution mode
            if ($config->debug !== null) {
                $bool = $config->debug;
                if (is_bool($bool)) {
                    $this->debug = $bool;
                }
            }
            
            if ($config->debug === true) {
                ini_set('display_errors', '1');
                error_reporting(E_ALL | E_STRICT | E_DEPRECATED | E_WARNING);
            }
    
            /* Define the default services */
            $this->serviceProvider->register(Config::class, function () use ($config) {
                return $config;
            });
            
            $this->serviceProvider->register(JsonStore::class, function () {
                return JsonStore::getInstance();
            });
            
            $this->serviceProvider->register(Request::class, function () {
                return new Request();
            });
            
            $this->serviceProvider->register(Connection::class, function () use ($config) {
                return new Connection(
                    $config->database->type,
                    $config->database->host,
                    $config->database->dbname,
                    $config->database->username,
                    $config->database->password,
                    $config->database->charset
                );
            });
    
            $this->serviceProvider->register(Database::class, function () {
                $connection = $this->serviceProvider->get(Connection::class);
                return new Database($connection);
            });
    
            $this->serviceProvider->register(BasicDatabase::class, function () use ($config) {
                return new BasicDatabase(
                    $config->database->type,
                    $config->database->host,
                    $config->database->dbname,
                    $config->database->username,
                    $config->database->password,
                    $config->database->charset
                );
            });
    
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
                    if (!is_null($config->localization->defaults->timezone)) {
                        $timezone = $config->localization->defaults->timezone;
                    } else {
                        $timezone = 'America/New_York';
                    }
                }
        
                date_default_timezone_set($timezone);
            } else {
                if (!is_null($config->localization->defaults->timezone)) {
                    date_default_timezone_set($config->localization->defaults->timezone);
                }
            }
    
            /*if (!$this->controllers->has('home')) {
                $this->registerController('home', \Apine\Controllers\System\HomeController::class);
            }
            
            if (!$this->controllers->has('error')) {
                $this->registerController('error', \Apine\Controllers\System\ErrorController::class);
            }*/
            
            $this->registerService('apiResources', $this->apiResources);
            
            //$router = new Router($this->serviceProvider, $config);
            $request = $this->serviceProvider->get(Request::class);
            $router = new Router($this->serviceProvider);
            //$route = $router->getRoute($request);
            $route = $router->find($request);
            //$response = $router->execute($route);
            $response = $router->run($route, $request);
            
            /*$webRouter = new WebRouter();
            $webRoute = $webRouter->route('/index');*/
            
            /*if ((isset($config->use_api) && $config->use_api === true) && $isAPICall) {
                $router = new APIRouter();
            } else {
                if ($isAPICall) {
                    throw new GenericException('RESTful API calls are not implemented', 501);
                }
        
                if ((empty($request) || $request == '/')) {
                    $request = '/index';
                }
        
                $router = new WebRouter();
            }*/
            
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
            /*$route = $router->route($request);
            $view = $router->execute($route->controller, $route->action, $route->args);*/
            
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
                
                if (!$this->debug) {
                    if ($error_name = $error->methodForCode($e->getCode())) {
                        $view = $error->$error_name();
                    } else {
                        $view = $error->server();
                    }
                } else {
                    $view = $error->custom($e->getCode(), $e->getMessage(), $e);
                }
                
                $view->draw();
            } catch (\Exception $e2) {
                var_dump($e2->getTraceAsString());
                $protocol = (isset($headers['SERVER_PROTOCOL']) ? $headers['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' 500 Internal Server Error');
                die("Critical Error : " . $e->getMessage());
            }
        } catch (\Exception $e) {
            // Handle PHP exceptions
            try {
                $error = new Controllers\ErrorController();
                $view = $error->custom(500, $e->getMessage(), $e);
                $view->draw();
            } catch (\Exception $e2) {
                $protocol = (isset($headers['SERVER_PROTOCOL']) ? $headers['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' 500 Internal Server Error');
                die("Critical Error : " . $e->getMessage());
            }
        }
    }
    
    public function registerService($className, $service)
    {
        $this->serviceProvider->register($className, $service);
    }
    
    /**
     * Register a controller for use as a resource
     * in the context of the RESTful API
     *
     * @param $name
     * @param $className
     *
     * @throws \Exception If the controller does not implements the APIActionsInterface
     */
    public function registerResource($name, $className)
    {
        /*if (!in_array('Apine\\MVC\\APIActionsInterface',
            class_implements($className))) {
            throw new \Exception(sprintf("%s is not a RESTful resource", $className));
        }*/
        
        $this->apiResources->register($name, $className);
    }
    
    /**
     * Return the current mode
     *
     * @return int
     */
    public function isDebugMode()
    {
        return $this->debug;
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
}