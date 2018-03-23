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
use Apine\Core\Error\ErrorHandler;
use Apine\Core\Http\Uri;
use Apine\Core\Routing\ResourcesContainer;
use Apine\Core\Routing\Router;
use Apine\Core\Http\Request;
use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Apine\Core\Config;
use Apine\Core\Utility\URLHelper;
use Apine\Core\Views\RedirectionView;
use Apine\Exception\GenericException;
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
    
    /**
     * @var string
     */
    private $includePath;
    
    /**
     * @var Container
     */
    private $services;
    
    /**
     * @var Container
     */
    private $apiResources;
    
    /**
     * Application constructor.
     */
    public function __construct(string $projectDirectory = null)
    {
        try {
            $this->setPaths($projectDirectory);
            
            ErrorHandler::set(1);
            $this->services= ServiceProvider::registerDefaultServices();
            $this->apiResources = new ResourcesContainer();
            
            // Verify if the minimum file dependencies are fulfilled
            if (!file_exists($documentRoot = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess') || !file_exists('settings.json')) {
                throw new GenericException('Critical Error: Framework Installation Not Completed', 503);
            }
        } catch (\Exception $e) {
            $this->outputException($e);
            die();
        }
        
    }
    
    /**
     * Move the include path to the project's root
     * rather than the server's root
     *
     * @param string|null $projectDirectory
     */
    private function setPaths(string $projectDirectory = null) : void
    {
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
    }
    
    /**
     * Run the application
     */
    public function run() : void
    {
        $config = null;
        $headers = getallheaders();
        $isHttp = (isset($headers['HTTPS']) && !empty($headers['HTTPS']));
        
        /**
         * Main Execution
         */
        try {
            // Verify is the protocol is allowed
            if (!$isHttp && !extension_loaded('xdebug')) {
                // Remove trailing slash
                $uri = rtrim($_SERVER['REQUEST_URI']);
                
                $redirection = new RedirectionView(new Uri(URLHelper::path($uri, APINE_PROTOCOL_HTTPS)), 301);
                $this->output($redirection->respond());
            }
    
            $config = new Config('settings.json');
    
            // Make sure application runs with a valid execution mode
            if ($config->debug !== null) {
                $bool = $config->debug;
                if (is_bool($bool) && $bool === true) {
                    ErrorHandler::set(1);
                }
            }
    
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
                    if (isset($config->localization->defaults->timezone)) {
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
    
            $request = Request::createFromGlobals();
            $router = new Router($this->services);
            $route = $router->find($request);
            $response = $router->run($route, $request);
            
            $this->output($response);
        } catch (\Throwable $e) {
            $this->outputException($e);
        }
    }
    
    /**
     * Output a response to the client
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function output(ResponseInterface $response) : void
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
        die;
    }
    
    /**
     * Output a caught exception to the client
     *
     * @param \Throwable $e
     */
    public function outputException(\Throwable $e) : void
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
    
    /**
     * @param string $className
     * @param callable|object $service
     */
    public function registerService(string $className, $service) : void
    {
        $this->services->register($className, $service);
    }
    
    /**
     * Register a controller for use as a resource
     * in the context of the RESTful API
     *
     * @param string $name
     * @param string $className
     *
     * @throws \Exception If the controller does not implements the APIActionsInterface
     */
    public function registerResource(string $name, string $className) : void
    {
        $this->apiResources->register($name, $className);
    }
}