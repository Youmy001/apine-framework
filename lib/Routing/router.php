<?php
/**
 * Application Router
 * This script contains an abstraction of a routing helper
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Abstraction of a request router
 *
 * @author Tommy Teasdale
 */
final class ApineRouter {
	
	/**
	 * Instance of the implementation
	 *
	 * @var ApineRouterInterface
	 */
	private $strategy;
	
	/**
	 * Instantiation of the strategy
	 */
	public function __construct () {
	
		if (ApineRequest::is_api_call()) {
			$this->strategy = new ApineAPIRouter();
		} else {
			$this->strategy = new ApineWebRouter();
		}
	
	}
	
	/**
	 * Route the request to the best matching controller and action
	 *
	 * @param string $request
	 * @return ApineRoute
	 */
	public function route ($request) {
	
		return $this->strategy->route($request);
	
	}
	
	/**
	 * Execute an action
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 */
	public function execute ($controller, $action, $args = null) {
	
		return $this->strategy->execute($controller, $action, $args);
	
	}
	
	/**
	 * Returns the route manager
	 *
	 * @return ApineRouterInterface
	 */
	public function get_handler () {
	
		return $this->strategy;
	
	}
	
}

interface ApineRouterInterface {
	
	/**
	 * Route the request to the best matching controller and action
	 *
	 * @param string $request
	 * @return ApineRoute
	 */
	public function route ($request);
	
	/**
	 * Execute an action
	 * 
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 */
	public function execute ($controller, $action, $args);
	
}