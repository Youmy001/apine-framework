<?php
/**
 * Request Router for API calls
 * This script contains a routing helper to route the request toward controllers
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Routing;

use \Exception as Exception;
use Apine\Core\Request as Request;
use Apine\Exception\GenericException as GenericException;

/**
 * API Request Router
 * Route requests toward the best matching controller. This is part of the MVC architecture
 *
 * @author Tommy Teasdale
 */
final class APIRouter implements RouterInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see ApineRouterInterface::route()
	 */
	public function route ($request) {
		
		try {
			$args = explode("/",$request);
			array_shift($args);
			
			$controller = $args[0];
			array_shift($args);
			
			// Add post arguments to args array
			if (Request::get_request_type() != "GET") {
				$args = array_merge($args, Request::post());
			}
			
			if (!empty(Request::files())) {
				$args = array_merge($args, array("uploads" => Request::files()));
			}
			
			$maj_controller = ucfirst($controller) . 'Controller';
			
			if (self::check_route($request)) {
				$route = new Route($controller, strtolower(Request::get_request_type()), $args);
			}
			
			if (!isset($route)) {
				throw new GenericException("Route \"$controller\" not Found", 404);
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
	 * @return boolean
	 */
	private function check_route ($route) {
		
		$args = explode("/",$route);
		array_shift($args);
		$controller = $args[0];
		
		try {
			$maj_controller = ucfirst($controller) . 'Controller';
			$return = false;
			
			if (class_exists('Apine\\Controllers\\User\\' . $maj_controller) && in_array('Apine\\MVC\\APIActionsInterface', class_implements('Apine\\Controllers\\' . $maj_controller))) {
				$return = 'Apine\\Controllers\\User\\' . $maj_controller;
			} else if (class_exists('Apine\\Controllers\\System\\' . $maj_controller) && in_array('Apine\\MVC\\APIActionsInterface', class_implements('Apine\\Controllers\\System\\' . $maj_controller))) {
				$return = 'Apine\\Controllers\\System\\' . $maj_controller;
			} else {
				
				if (class_exists($maj_controller)) {
					$return = $maj_controller;
				} else if (file_exists('controllers/' . $controller . '_controller.php')) {
					require_once('controllers/' . $controller . '_controller.php');
						
					if (class_exists($maj_controller)) {
						$return = $maj_controller;
					}
				}
			}
		
			return $return;
		} catch (Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
		
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see ApineRouterInterface::execute()
	 */
	public function execute ($controller, $method, $args = null) {
		
		$controller_name = self::check_route("/$controller/$method");
		
		if ($controller_name !== false) {
			$controller = new $controller_name();
			return $controller->$method($args);
		} else {
			throw new GenericException("Route \"$controller\" Not found", 404);
		}
		
	}
	
}