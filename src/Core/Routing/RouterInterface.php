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
}