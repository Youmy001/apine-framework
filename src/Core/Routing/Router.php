<?php
/**
 * Router
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Routing;

use Apine\Core\Controllers\Controller;
use Apine\Core\Error\Http\NotFoundException;
use Apine\Core\Views\View;
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
    
    public static $verbs = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'HEAD',
        'OPTIONS',
        'TRACE',
        'PATCH'
    ];
    
    /**
     * Router constructor.
     *
     * @param Container $container
     */
    public function __construct(Container &$container)
    {
        $this->container = $container;
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
     * Add route
     *
     * @param string[]      $methods
     * @param string        $pattern
     * @param string        $controller
     * @param null|string   $action
     * @param string[]      $middlewares
     *
     * @throws \Exception If the controller or the action do not exist
     */
    public function map(array $methods, string $pattern, string $controller, ?string $action, array $middlewares = [])
    {
        if (!class_exists($controller) || !is_subclass_of($controller, Controller::class) || !method_exists($controller, $action)) {
            throw new \Exception('Controller or method not found');
        }
        
        foreach ($methods as $method) {
            $this->routes[] = new Route($method, $pattern, $controller, $action);
        }
    }
    
    /**
     * Add multiple routes under a prefix
     *
     * @param string   $pattern
     * @param callable $callable
     */
    public function group(string $pattern, callable $callable)
    {
        // First execute the callable
        $group = new RouteGroup($pattern, $callable);
        $group->resolve();
        
        $this->routes = array_merge($this->routes, $group->getRoutes());
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function get(string $pattern, string $controller, ?string $action)
    {
        $this->map(['GET'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function post(string $pattern, string $controller, ?string $action)
    {
        $this->map(['POST'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function put(string $pattern, string $controller, ?string $action)
    {
        $this->map(['PUT'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function delete(string $pattern, string $controller, ?string $action)
    {
        $this->map(['DELETE'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function options(string $pattern, string $controller, ?string $action)
    {
        $this->map(['OPTIONS'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function head(string $pattern, string $controller, ?string $action)
    {
        $this->map(['HEAD'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function trace(string $pattern, string $controller, ?string $action)
    {
        $this->map(['TRACE'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function patch(string $pattern, string $controller, ?string $action)
    {
        $this->map(['PATCH'], $pattern, $controller, $action);
    }
    
    /**
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     *
     * @throws \Exception
     */
    public function any(string $pattern, string $controller, ?string $action)
    {
        $this->map(self::$verbs, $pattern, $controller, $action);
    }
}