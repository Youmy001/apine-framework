<?php
/**
 * Router
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Routing;


use \ReflectionClass;
use \ReflectionMethod;
use Apine\Core\Request;
use Apine\Core\Config;
use Apine\Core\Container\Container;
use Apine\Core\Http\Response;
use Apine\Core\JsonStore;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Apine\Exception\GenericException;

class Router implements RouterInterface
{
    /**
     * @var Request
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
     * @param Request $request
     * @return Route|null
     *
     * @throws \Exception If route not found
     */
    public function find (Request $request) : Route
    {
        $requestString = $request->getAction();
        $requestMethod = $request->getMethod();
        
        foreach ($this->routes as $route) {
            if ($route->match($requestString, $requestMethod)) {
                $this->current = $route;
                $this->request = $request;
                break;
            }
        }
        
        if (null === $this->current) {
            throw new GenericException(sprintf("Route for request %s not found", $requestString), 404);
        }
        
        return $this->current;
    }
    
    /**
     * @return ResponseInterface
     * @throws \Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
            $request = $request->withQueryParams($requestParams);
            $container->register(Request::class, $request);
            $_GET = array_merge($_GET, $requestParams);
            
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
            
            $result = $method->invokeArgs($controller, $parameters);
            
            $headers = getallheaders();
            $protocol = (isset($headers['SERVER_PROTOCOL']) ? $headers['SERVER_PROTOCOL'] : 'HTTP/1.0');
            $response = new Response(200);
            // TODO Insert view in the response
            return $response->withProtocolVersion($protocol);
        } catch (\Exception $e) {
            throw new $e;
        }
    }
    
    /**
     * @param Route         $route
     * @param Request       $request
     *
     * @return ResponseInterface
     * @throws \Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(Route $route, Request $request) : ResponseInterface
    {
        try {
            $this->current = $route;
            $this->request = $request;
            
            if (!$this->current->match($request->getAction(), $request->getMethod())) {
                throw new \Exception(sprintf("Route does not match request %s", $request->getAction()), 404);
            }
            
            return $this->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Request $request
     *
     * @return ResponseInterface
     * @throws \Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dispatch(Request $request) : ResponseInterface
    {
        try {
            $this->find($request);
            return $this->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @throws \ErrorException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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