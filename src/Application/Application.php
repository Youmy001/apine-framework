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
use Apine\Core\Error\Http\HttpException;
use Apine\Core\Http\Uri;
use Apine\Core\Routing\Router;
use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Apine\Core\Config;
use Apine\Core\Utility\URLHelper;
use Apine\Core\Views\RedirectionView;
use Psr\Http\Message\ResponseInterface;

use const Apine\Core\PROTOCOL_HTTPS;

use function Apine\Core\Utility\executionTime;

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
     * Application constructor.
     */
    public function __construct(string $projectDirectory = null)
    {
        try {
            executionTime();
            $this->setPaths($projectDirectory);
            
            ErrorHandler::set(1);
            $this->services= ServiceProvider::registerDefaultServices();
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
                $helper = new URLHelper();
                $uri = rtrim($_SERVER['REQUEST_URI']);
                
                $redirection = new RedirectionView(new Uri($helper->path($uri, PROTOCOL_HTTPS)), 301);
                $this->output($redirection->respond());
            }
    
            $config = new Config('config/error.json');
    
            // Make sure application runs with a valid execution mode
            if ($config->debug !== null) {
                $bool = $config->debug;
                if (is_bool($bool) && $bool === true) {
                    ErrorHandler::set(1);
                } else {
                    ErrorHandler::set(0);
                }
            }
    
            $request = $this->services->get('request');
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
    
        if ($e instanceof HttpException) {
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
     * @param callable|mixed $service
     */
    public function registerService(string $className, $service) : void
    {
        $this->services->register($className, $service);
    }
}