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
use Apine\Core\Error\ErrorHandler;
use Apine\Core\JsonStore;
use Apine\Core\Routing\ResourcesContainer;
use Apine\Core\Routing\Router;
use Apine\Core\Http\Request;
use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Apine\Core\Config;
use Apine\Exception\GenericException;
use Apine\Controllers\System as Controllers;
use Apine\Utility\Routes;
use Psr\Http\Message\ResponseInterface;

/**
 * Apine Application
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Application
 */
final class Application
{
    /**
     * Version number of the framework
     *
     * @var string
     */
    public static $version = '2.0.0-dev';
    
    /**
     * Name of the folder where the framework is located
     *
     * @var string
     */
    private $apineFolder;
    
    private $includePath;
    
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
     * @deprecated
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
    public function __construct(string $projectDirectory = null)
    {
        ErrorHandler::set(1);
        $this->serviceProvider = ServiceProvider::getInstance();
        $this->apiResources = new ResourcesContainer();
        
        try {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $this->apineFolder = realpath(dirname(__FILE__) . '/..'); // The path to the framework itself
            
            if (null === $projectDirectory) {
                $directory = $documentRoot;
                
                while (!file_exists($directory . '/composer.json')) {
                    $directory = dirname($directory);
                }
                
                $projectDirectory = $directory;
            }
    
            $this->includePath = $projectDirectory;
            set_include_path($this->includePath);
            chdir($this->includePath);
    
            // Verify if the minimum file dependencies are fulfilled
            if (!file_exists($documentRoot . '/.htaccess') || !file_exists('settings.json')) {
                throw new GenericException('Critical Error: Framework Installation Not Completed', 503);
            }
        } catch (\Exception $e) {
            //ErrorHandler::handleException($e);
            $this->outputException($e);
            die();
        }
        
    }
    
    /**
     * Set the path to the application from the root for the virtual server
     * The application tries by default to guess it.
     *
     * @param string $a_webroot
     * @deprecated
     */
    public function setWebroot($a_webroot = '')
    {
        $this->webroot = $a_webroot;
    }
    
    /**
     * Run the application
     */
    public function run()
    {
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
                if (is_bool($bool) && $bool === true) {
                    $this->debug = true;
                    ErrorHandler::set(1);
                }
            }
    
            /* Define the default services */
            $this->serviceProvider->register(Config::class, function () use ($config) {
                return $config;
            });
    
            $this->serviceProvider->register(JsonStore::class, function () {
                return JsonStore::getInstance();
            });
    
            $this->serviceProvider->register(Request::class, function () {
                return Request::createFromGlobals();
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
                $gi = geoip_open($this->apineFolder . DIRECTORY_SEPARATOR . "Includes" . DIRECTORY_SEPARATOR . "GeoLiteCity.dat",
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
    
            $this->registerService('apiResources', $this->apiResources);
    
            //$router = new Router($this->serviceProvider, $config);
            //$request = $this->serviceProvider->get(Request::class);
            $request = Request::createFromGlobals();
            $router = new Router($this->serviceProvider);
            $route = $router->find($request);
            $response = $router->run($route, $request);
            
            $this->output($response);
        } catch (\Throwable $e) {
            //ErrorHandler::handleException($e);
            $this->outputException($e);
        }
    }
    
    public function output(ResponseInterface $response)
    {
        if (!headers_sent()) {
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        
            foreach ($response->getHeaders() as $name => $values) {
                if (is_array($values)) {
                    $values = implode(", ", $values);
                }
            
                header(sprintf('%s: %s', $name, $values), false);
            }
        }
        
        $body = $response->getBody();
    
        if ($body->isSeekable()) {
            $body->rewind();
        }
    
        print $body->getContents();
    }
    
    public function outputException(\Throwable $e)
    {
        $response = new Response(500);
        $response = $response->withAddedHeader('Content-Type', 'text/plain');
    
        if ($e instanceof GenericException) {
            $response = $response->withStatus($e->getCode());
        }
    
        $result = $e->getMessage() . "\n\r";
    
        if (ErrorHandler::$reportingLevel === 1) {
            $trace = explode("\n", $e->getTraceAsString());
        
            foreach ($trace as $step) {
                $result .= "\n";
                $result .= $step;
            }
        }
    
        $content = new Stream(fopen('php://memory', 'r+'));
        $content->write($result);
    
        $response = $response->withBody($content);
        
        $this->output($response);
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
     * @deprecated
     */
    public function getWebroot()
    {
        return $this->webroot;
    }
}