<?php
/**
 * Request Router for API calls
 * This script contains a routing helper to route the request toward controllers
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * API Request Router
 * Route requests toward the best matching controller. This is part of the MVC architecture
 *
 * @author Tommy Teasdale
 */
final class ApineAPIRouter implements ApineRouterInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see ApineRouterInterface::route()
	 */
	public function route ($request) {
		
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
		
		try {
			$maj_controller = ucfirst($controller) . 'Controller';
			
			if (self::check_route($request)) {
				$route = new ApineRoute($controller, strtolower(Request::get_request_type()), $args);
			}
			
			if (!isset($route)) {
				throw new ApineException("Route \"$controller\" not Found", 404);
			}
			
			return $route;
			
		} catch (Exception $e) {
			throw new ApineException($e->getMessage(), $e->getCode(), $e);
		}
		
	}
	
	private function check_route ($route) {
		
		$args = explode("/",$route);
		array_shift($args);
		$controller = $args[0];
		
		try {
			$maj_controller = ucfirst($controller) . 'Controller';
			$route = false;
				
			if (class_exists($maj_controller) && in_array('APIActions', class_implements($maj_controller))) {
				$route = true;
			}else if (file_exists('controllers/' . $controller . '_controller.php')) {
				require_once('controllers/' . $controller . '_controller.php');
		
				if (class_exists($maj_controller)) {
					//print "Found";
					$route = true;
				}
			}
		
			return $route;
		} catch (Exception $e) {
			throw new ApineException($e->getMessage(), $e->getCode(), $e);
		}
		
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see ApineRouterInterface::execute()
	 */
	public function execute ($controller, $method, $args = null) {
		
		if (self::check_route("/$controller")) {
			$maj_controller = ucfirst($controller) . 'Controller';
			$controller = new $maj_controller();
			$controller->$method($args);
		} else {
			throw new ApineException("Route \"$controller\" Not found", 404);
		}
		
	}
	
}