<?php
/**
 * Router
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Routing;

use Apine\Core\Views\View;
use \ReflectionClass;
use \ReflectionMethod;
use Apine\Core\Config;
use Apine\Core\Container\Container;
use Apine\Core\Http\Response;
use Apine\Core\Json\JsonStore;
use Apine\Exception\GenericException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;
    
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
     * @throws GenericException
     */
    public function __construct(Container &$container)
    {
        $this->container = $container;
    
        try {
            $config = $container->get(Config::class);
            $request = $_GET['apine-request'];
            $requestArray = explode("/", $request);
            $isAPICall = ($requestArray[1] === 'api');
        
            if ((isset($config->use_api) && $config->use_api === true) && $isAPICall) {
                $resources = $container->get('apiResources');
                $this->loadResources($resources);
            } else {
                $this->loadRoutes();
            }
        } catch (\Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
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
        
        /* Strip the REST call marker from the path*/
        if (substr($requestString, 0 , 4) === '/api') {
            $requestString = substr($requestString, 4);
        }
        
        foreach ($this->routes as $route) {
            if ($route->match($requestString, $requestMethod)) {
                $this->current = $route;
                $this->request = $request;
                break;
            }
        }
        
        if (null === $this->current) {
            throw new GenericException(sprintf("Route for request %s not found", $request->getUri()->getPath()), 404);
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
    
            /* Strip the REST call marker from the path*/
            if (substr($requestString, 0 , 4) === '/api') {
                $requestString = substr($requestString, 4);
            }
            
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
        $config = $this->container->get(Config::class);
        $routes = JsonStore::get($config->getPath())->routes;
        $routes = json_decode(json_encode($routes), true);
        
        array_walk($routes, function ($definitions, $pattern) {
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
    
    /**
     * @param ResourcesContainer $resources
     *
     * @throws \ReflectionException
     */
    private function loadResources(ResourcesContainer $resources)
    {
        foreach ($resources->toArray() as $name => $class) {
            $reflection = new ReflectionClass($class);
            
            $this->routes = array_merge(
                $this->routes,
                array_map(
                    function (ReflectionMethod $method) use ($name, $class) {
                        return new Route(
                            strtoupper($method->getName()),
                            '/' . $name,
                            $class,
                            $method->getName(),
                            [],
                            true
                        );
                    }, array_filter(
                        $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                        function ( ReflectionMethod $method) {
                            return in_array(strtoupper($method->getName()), self::$verbs, true);
                        }
                    )
                )
            );
        }
    }
}