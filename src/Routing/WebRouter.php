<?php
/**
 * Request Router for Web apps
 * This script contains a routing helper to route the request toward controllers
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Routing;

use Apine\Core\JsonStore;
use \Exception as Exception;
use Apine\Core\Request as Request;
use Apine\Exception\GenericException;
use Apine\MVC\View;

/**
 * Web App Request Router
 * Route requests toward the best matching controller. This is part of the MVC architecture
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Routing
 */
class WebRouter implements RouterInterface
{
    private $routes_file;
    
    final public function __construct($a_path = 'settings.json')
    {
        try {
            $this->routes_file = $a_path;
        } catch (Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Find a matching route in JSON the route configuration and return a modified request string
     *
     * @param string $request
     *
     * @return mixed
     * @throws GenericException
     */
    final private function json_route($request)
    {
        /*$path = $this->routes_file;
        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        $json_object = json_decode($content);
        
        if (isset($json_object->routes)) {
            $routes = $json_object->routes;
        } else {
            $routes = null;
        }*/
        
        $settings = JsonStore::get($this->routes_file);
        
        if (isset($settings->routes)) {
            $routes = $settings->routes;
        } else {
            $routes = null;
        }
        
        $json_error = json_last_error();
        if ($routes === null && $json_error !== JSON_ERROR_NONE) {
            throw new GenericException('Error Loading Routes', $json_error);
        }
        
        foreach ($routes as $item => $values) {
            $method = $_SERVER['REQUEST_METHOD'];
            
            if (isset($values->$method)) {
                $route = $values->$method;
                $match = str_ireplace('/', '\\/', $item);
                $match = '/^' . $match . '$/';
                $replace = "/{$route->controller}/{$route->action}";
                
                if ($route->args === true) {
                    $number_args = (!empty($route->argsnum)) ? $route->argsnum : preg_match_all("/(\(.*?\))/", $match);
                    
                    for ($i = 1; $i <= $number_args; $i++) {
                        $replace .= "/$" . $i;
                    }
                }
                
                if (preg_match($match, $request)) {
                    $request = preg_replace($match, $replace, $request);
                    break;
                }
            }
        }
        
        return $request;
    }
    
    /**
     * {@inheritDoc}
     * @see ApineRouterInterface::route()
     */
    final public function route($request)
    {
        $route_found = false;
        
        $vanilla_route_found = self::check_route($request);
        
        if (!$vanilla_route_found && file_exists($this->routes_file)) {
            $request = $this->json_route($request);
            $route_found = true;
        }
        
        $args = explode("/", $request);
        array_shift($args);
        
        if (count($args) > 1) {
            $controller = $args[0];
            array_shift($args);
            $action = $args[0];
            array_shift($args);
        } else {
            if (count($args) > 0) {
                $controller = $args[0];
                array_shift($args);
                $action = "index";
            } else {
                $controller = null;
                $action = null;
            }
        }
        
        // Add post arguments to args array
        $args = array_merge($args, Request::getRequestParams());
        
        try {
            if ($this->checkRoute($request)) {
                $route = new Route($controller, $action, $args);
            }
            
            if (!isset($route)) {
                if ($route_found) {
                    throw new GenericException("Reference Found but Action not Accessible for Route \"$controller\"",
                        410);
                } else {
                    throw new GenericException("Route \"$controller\" not Found", 404);
                }
            }
            
            return $route;
            
        } catch (Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Verifies if the request string matches an existing controller
     *
     * @param string $a_route
     *
     * @return boolean
     * @throws GenericException
     */
    private function checkRoute($a_route)
    {
        $args = explode("/", $a_route);
        array_shift($args);
        
        if (count($args) > 1) {
            $controller = $args[0];
            array_shift($args);
            $action = $args[0];
            array_shift($args);
        } else {
            $controller = $args[0];
            array_shift($args);
            $action = "index";
        }
        
        try {
            $maj_controller = str_replace('_', '', ucwords($controller, "_")) . 'Controller';
            $return = false;
            
            if (class_exists('Apine\\Controllers\\User\\' . $maj_controller) && method_exists('Apine\\Controllers\\User\\' . $maj_controller,
                    $action)
            ) {
                $return = 'Apine\\Controllers\\User\\' . $maj_controller;
            } else {
                if (class_exists('Apine\\Controllers\\System\\' . $maj_controller) && method_exists('Apine\\Controllers\\System\\' . $maj_controller,
                        $action)
                ) {
                    $return = 'Apine\\Controllers\\System\\' . $maj_controller;
                } else {
                    if (class_exists($maj_controller) && method_exists($maj_controller, $action)) {
                        $return = $maj_controller;
                    } else {
                        if (file_exists('controllers/' . $controller . '_controller.php')) {
                            require_once('controllers/' . $controller . '_controller.php');
                            
                            if (method_exists($maj_controller, $action)) {
                                $return = $maj_controller;
                            }
                        }
                    }
                }
            }
            
            return $return;
        } catch (Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * @return View
     * @throws GenericException
     * {@inheritDoc}
     * @see ApineRouterInterface::execute()
     */
    final public function execute($controller, $action, $args = null)
    {
        $controller_name = self::checkRoute("/$controller/$action");
        
        if ($controller_name !== false) {
            $controller = new $controller_name();
            
            return $controller->$action($args);
        } else {
            throw new GenericException("Route \"$controller\" Not found", 404);
        }
    }
}