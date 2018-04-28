<?php
/**
 * Interface for routers
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

namespace Apine\Core\Routing;

//use Apine\Core\Request;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface RouterInterface
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Routing
 */
interface RouterInterface
{
    /**
     * Route the request to the best matching controller and action
     *
     * @param Request $request
     *
     * @return Route
     */
    public function find(Request $request) : Route;
    
    /**
     * Execute an action
     *
     * @param Route         $route
     * @param Request       $request
     *
     * @return ResponseInterface
     */
    public function run(Route $route, Request $request) : ResponseInterface;
    
    /**
     * Execute an action
     *
     * @param Request $request
     *
     * @return ResponseInterface
     */
    public function dispatch(Request $request) : ResponseInterface;
    
    /**
     * Add route
     *
     * @param string[]    $methods
     * @param string      $pattern
     * @param string      $controller
     * @param null|string $action
     * @param array|null  $middlewares
     */
    public function map(array $methods, string $pattern, string $controller, ?string $action, array $middlewares = []);
    
    /**
     * Add multiple routes under a prefix
     *
     * @param string   $patern
     * @param callable $callable
     */
    public function group(string $patern, callable $callable);
    
    /*public function get(string $pattern, string $controller, ?string $action);
    
    public function post(string $pattern, string $controller, ?string $action);
    
    public function put(string $pattern, string $controller, ?string $action);
    
    public function delete(string $pattern, string $controller, ?string $action);
    
    public function options(string $pattern, string $controller, ?string $action);
    
    public function head(string $pattern, string $controller, ?string $action);
    
    public function trace(string $pattern, string $controller, ?string $action);
    
    public function patch(string $pattern, string $controller, ?string $action);
    
    public function any(string $pattern, string $controller, ?string $action);*/
}