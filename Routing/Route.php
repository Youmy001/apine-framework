<?php
/**
 * Basic Route representation
 * This script contains a basic representation of a routes
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Routing;

/**
 * Basic representation of a route
 * 
 * @author Tommy Teasdale
 */
final class Route {
	
	/**
	 * Name of a controller
	 * 
	 * @var string
	 */
	public $controller;
	
	/**
	 * Name of an action method
	 * 
	 * @var string
	 */
	public $action;
	
	/**
	 * Parameters to pass to the action method
	 * @var array
	 */
	public $args;
	
	/**
	 * Instantiation of a route
	 *
	 * @param string $controller
	 * @param string $action
	 * @param string $args
	 */
	public function __construct($controller, $action, $args = array()) {
		
		$this->controller = $controller;
		$this->action = $action;
		$this->args = $args;
		
	}
	
}