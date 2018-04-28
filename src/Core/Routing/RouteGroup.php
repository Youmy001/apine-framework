<?php
/**
 * RouteGroup
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Routing;

use Apine\Core\Controllers\Controller;

/**
 * Class RouteGroup
 *
 * @package Apine\Core\Routing
 */
class RouteGroup
{
    /**
     * @var Route[]
     */
    private $routes = [];
    
    /**
     * @var string
     */
    private $pattern;
    
    /**
     * @var callable
     */
    private $callable;
    
    /**
     * RouteGroup constructor.
     *
     * @param string   $pattern
     * @param callable $callable
     */
    public function __construct(string $pattern, callable $callable)
    {
        $this->pattern = $pattern;
        $this->callable = $callable;
    }
    
    public function resolve()
    {
        $callable = $this->callable;
        $callable($this);
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
    
        $pattern = $this->appendPrefix($pattern, $this->pattern);
        
        foreach ($methods as $method) {
            $this->routes[] = new Route($method, $pattern, $controller, $action);
        }
    }
    
    public function getRoutes() : array
    {
        return $this->routes;
    }
    
    private function appendPrefix(string $pattern, string $prefix = "") : string
    {
        if (!empty($prefix)) {
            $pattern = "/$prefix" . $pattern;
        }
        
        return $pattern;
    }
}