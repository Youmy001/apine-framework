<?php
/**
 * Request Router for Web apps
 * This script contains a routing helper to route the request toward controllers
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Routing;

use \Exception as Exception;
use Apine\Application\Application as Application;
use Apine\Core\Request as Request;
use Apine\XML as XML;
use Apine\Exception\GenericException as GenericException;
use Apine\MVC\View;

/**
 * Web App Request Router
 * Route requests toward the best matching controller. This is part of the MVC architecture
 * 
 * @author Tommy Teasdale
 */
class WebRouter implements RouterInterface {
	
	private $routes_file;
	
	private $routes_type;
	
	final public function __construct ($a_path= 'routes.json', $a_type = APINE_ROUTES_JSON) {
		
		try {
			//$application = Application::get_instance();
			//$this->routes_file = $application->get_routes_path();
			//$this->routes_type = $application->get_routes_type();
			$this->routes_file = $a_path;
			$this->routes_type = $a_type;
			
			/*if (!file_exists($this->routes_file)) {
				throw new GenericException('Route File Not Found', 418);
			}*/
		} catch (Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
		
	}
	
	/**
	 * Find matching route in XML route configuration and return modified request string 
	 * 
	 * @return mixed
	 */
	final private function xml_route ($request) {
		
		$xml_routes = new XML\Parser();
		$xml_routes->load_from_file($this->routes_file);
		$route_found = false;
		
		$routes = $xml_routes->getElementsByAttributeValue('method', Request::get_request_type());
		
		$str_routes = "";
		$found_route = null;
		
		foreach ($routes as $item) {
			
			if ($item->nodeType == XML_ELEMENT_NODE) {
				foreach ($item->childNodes as $attr) {
		        	if ($attr->nodeType == XML_ELEMENT_NODE){
		        		if ($attr->tagName == "request") {
		        			if ($item->getAttribute('method') == $_SERVER['REQUEST_METHOD']) {
		        				$match_route = $item->cloneNode(true);
		        				
		        				$controller = $match_route->getElementsByTagName('controller')->item(0)->nodeValue;
		        				$action = $match_route->getElementsByTagName('action')->item(0)->nodeValue;
		        				
		        				$match = str_ireplace('/','\\/',$match_route->getElementsByTagName('request')->item(0)->nodeValue);
		        				$match = '/^' . $match . '$/';
		        				$replace = "/$controller/$action";
		        				
		        				if ($match_route->getAttribute('args') == true) {
		        					$number_args = (!empty($match_route->getAttribute('argsnum'))) ? $match_route->getAttribute('argsnum') : preg_match_all("/(\(.*?\))/", $match);
		        					
		        					for ($i = 1; $i <= $number_args; $i++) {
		        						$replace .= "/$" . $i;
		        					}
		        				}
		        				
		        				if(preg_match($match, $request)){
		        					$request = preg_replace($match, $replace, $request);
		        					$found_route = $item->cloneNode(true);
		        					break;
		        				}
		        			}
		        		}
		        	}
				}
		    }
		    
		    if ($found_route !== null) {
		    	break;
		    }
		}
		
		return $request;
		
	}
	
	/**
	 * Find a matching route in JSON the route configuration and return a modified request string 
	 * 
	 * @param string $request
	 * @return mixed
	 */
	final private function json_route ($request) {
		
		$path = $this->routes_file;
		$file = fopen($path, 'r');
		$content = fread($file, filesize($path));
		$routes = json_decode($content);
		
		$json_error = json_last_error();
		if ($routes === null && $json_error !== JSON_ERROR_NONE) {
			throw new GenericException('Error Loading JSON file', $json_error);
		}
		
		foreach ($routes as $item => $values) {
			$method = $_SERVER['REQUEST_METHOD'];
			
			if (isset($values->$method)) {
				$route = $values->$method;
				$match = str_ireplace('/','\\/', $item);
				$match = '/^' . $match . '$/';
				$replace = "/{$route->controller}/{$route->action}";
				
				if ($route->args === true) {
					$number_args = (!empty($route->argsnum)) ? $route->argsnum : preg_match_all("/(\(.*?\))/", $match);
				
					for ($i = 1; $i <= $number_args; $i++) {
						$replace .= "/$" . $i;
					}
				}
				
				if(preg_match($match, $request)){
					$request = preg_replace($match, $replace, $request);
					break;
				}
			}
		}
		
		return $request;
		
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see ApineRouterInterface::route()
	 */
	final public function route ($request) {
		
		$route_found = false;
		
		$vanilla_route_found = self::check_route($request);
		
		if (!$vanilla_route_found && file_exists($this->routes_file)) {
			switch ($this->routes_type) {
				case APINE_ROUTES_JSON:
					$file_request = $this->json_route($request);
					break;
				case APINE_ROUTES_XML:
					$file_request = $this->xml_route($request);
					break;
				default:
					$file_request = null;
			}
			
			if ($file_request !== $request) {
				$route_found = true;
				$request = $file_request;
			}
		}
		
		$args = explode("/",$request);
		array_shift($args);
		
		if (count($args) > 1) {
			$controller = $args[0];
			array_shift($args);
			$action = $args[0];
			array_shift($args);
		} else if (count($args) > 0) {
			$controller = $args[0];
			array_shift($args);
			$action = "index";
		} else {
			$controller = null;
		}
		
		// Add post arguments to args array
		if (Request::get_request_type() != "GET") {
			$args = array_merge($args, Request::post());
		}
		
		if (!empty(Request::files())) {
			$args = array_merge($args, array("uploads" => Request::files()));
		}
		
		try {
			if ($this->check_route($request)) {
				$route = new Route($controller, $action, $args);
			}
			
			if (!isset($route)) {
				if ($route_found) {
					throw new GenericException("Reference Found but Action not Accessible for Route \"$controller\"", 410);
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
	 * @return boolean
	 */
	private function check_route ($a_route) {
		
		$args = explode("/",$a_route);
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
			$maj_controller = ucfirst($controller) . 'Controller';
			
			$return = false;
			
			if (class_exists('Apine\\Controllers\\User\\' . $maj_controller) && method_exists('Apine\\Controllers\\User\\' . $maj_controller, $action)) {
				$return = 'Apine\\Controllers\\User\\' . $maj_controller;
			} else if (class_exists('Apine\\Controllers\\System\\' . $maj_controller) && method_exists('Apine\\Controllers\\System\\' . $maj_controller, $action)) {
				$return = 'Apine\\Controllers\\System\\' . $maj_controller;
			} else {
				if (class_exists($maj_controller) && method_exists($maj_controller, $action)) {
					$return = $maj_controller;
				} else if (file_exists('controllers/' . $controller . '_controller.php')) {
					require_once('controllers/' . $controller . '_controller.php');
					
					if (method_exists($maj_controller, $action)) {
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
	 * @return View
	 * {@inheritDoc}
	 * @see ApineRouterInterface::execute()
	 */
	final public function execute ($controller, $action, $args = null) {
		
		$controller_name = self::check_route("/$controller/$action");
		
		if ($controller_name !== false) {
			$controller = new $controller_name();
			return $controller->$action($args);
		} else {
			throw new GenericException("Route \"$controller\" Not found", 404);
		}
		
	}
	
}