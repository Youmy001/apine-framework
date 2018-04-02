<?php
/**
 * Router
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Routing;

use Apine\Core\Error\Http\NotFoundException;
use Apine\Core\Views\View;
use \ReflectionClass;
use \ReflectionMethod;
use Apine\Core\Config;
use Apine\Core\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var Route[]
     */
    private $routes = [];
    
    /**
     * @var Route
     */
    private $current;
    
    /**
     * @var Container
     */
    private $container;
    
    private static $verbs = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'HEAD',
        'OPTIONS',
        'TRACE'
    ];
    
    /**
     * Router constructor.
     *
     * @param Container $container
     *
     * @throws \Exception
     */
    public function __construct(Container &$container)
    {
        $this->container = $container;
    
        try {
            $this->config = new Config('config/router.json');
            
            if ($this->config->serve->api === true) {
                $this->loadAPIRoutes();
            }
            
            if ($this->config->serve->web === true)  {
                $this->loadRoutes();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Find the best matching controller and action for the request
     *
     * @param ServerRequestInterface $request
     * @return Route
     *
     * @throws \Exception If route not found
     */
    public function find (ServerRequestInterface $request) : Route
    {
        $requestString = $request->getUri()->getPath();
        $requestMethod = $request->getMethod();
        
        foreach ($this->routes as $route) {
            if ($route->match($requestString, $requestMethod)) {
                $this->current = $route;
                $this->request = $request;
                break;
            }
        }
        
        if (null === $this->current) {
            throw new NotFoundException(sprintf("Route for request %s not found", $request->getUri()->getPath()));
        }
        
        return $this->current;
    }
    
    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    private function execute() : ResponseInterface
    {
        try {
            $container = $this->container;
            $route = $this->current;
            $request = $this->request;
            
            $reflection = new \ReflectionClass($route->controller);
            $constructor = $reflection->getConstructor();
            
            $method = $reflection->getMethod($route->action);
            $requestParams = DependencyResolver::mapParametersForRequest($request, $route);
            
            /* Execution of the user code
             *
             * Instantiate de controller then
             * call the action method
             */
            if ($constructor !== null) {
                $constructorParameters = DependencyResolver::mapConstructorArguments($container, $constructor->getParameters());
                
                $controller = $reflection->newInstanceArgs($constructorParameters);
            } else {
                $controller = $reflection->newInstanceWithoutConstructor();
            }
            
            $parameters = DependencyResolver::mapActionArguments($container, $requestParams, $route->actionParameters);
            
            $response = $method->invokeArgs($controller, $parameters);
            
            if ($response instanceof View) {
                $response = $response->respond();
            }
    
            if (!($response instanceof ResponseInterface)) {
                throw new \RuntimeException(sprintf('%s::%s must return an instance of %s or %s', $route->controller, $route->action, ResponseInterface::class, View::class));
            }
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Route $route
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(Route $route, ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $this->current = $route;
            $this->request = $request;
            
            $requestString = $request->getUri()->getPath();
            
            if (!$this->current->match($requestString, $request->getMethod())) {
                throw new \Exception(sprintf("Route does not match request %s", $request->getUri()->getPath()), 404);
            }
            
            return $this->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function dispatch(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $this->find($request);
            return $this->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @throws \Exception
     */
    private function loadRoutes ()
    {
        $routes = json_decode(file_get_contents('config/routes/web.json'), true);
        $prefix = $this->config->prefixes->web;
        
        array_walk($routes, function ($definitions, $pattern) use ($prefix) {
            
            $pattern = $this->appendPrefix($pattern, $prefix);
            $this->routes = array_merge(
                $this->routes,
                array_map(function($method, $definition) use ($pattern) {
                    if(!isset($definition['parameters'])) {
                        $definition['parameters'] = [];
                    }
                    
                    return new Route(
                        $method,
                        $pattern,
                        $definition['controller'],
                        $definition['action'],
                        $definition['parameters']
                    );
                }, array_keys($definitions), $definitions)
            );
        });
    }
    
    private function loadAPIRoutes()
    {
        $routes = json_decode(file_get_contents('config/routes/api.json'), true);
        $prefix = $this->config->prefixes->api;
        
        array_walk($routes, function($definitions, $pattern) use ($prefix, &$computed) {
            $pattern = $this->appendPrefix($pattern, $prefix);
            $class= $definitions['controller'];
            $reflection = new ReflectionClass($definitions['controller']);
            unset($definitions['controller']);
            
            $this->routes = array_merge(
                $this->routes,
                array_map(
                    function(ReflectionMethod $method) use ($pattern, $class, $prefix, $definitions) {
                        $requestMethod = strtoupper($method->getName());
                        $parameters = [];
                        
                        if (isset($definitions[$requestMethod]['parameters'])) {
                            $parameters = $definitions[$requestMethod]['parameters'];
                        }
                        
                        return new Route(
                            $requestMethod,
                            $pattern,
                            $class,
                            $method->getName(),
                            $parameters,
                            true
                        );
                    }, array_filter(
                        $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                        function (ReflectionMethod $method) {
                            return in_array(strtoupper($method->getName()), self::$verbs, true);
                        }
                    )
                )
            );
        });
    }
    
    private function appendPrefix(string $pattern, string $prefix = "") : string
    {
        if (!empty($prefix)) {
            $pattern = "/$prefix" . $pattern;
        }
        
        return $pattern;
    }
}