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
	 * Instance of the Route Manager
	 * Singleton Implementation
	 *
	 * @var ApineRouter
	 */
	private static $_instance;
	
	/**
	 * Instantiation of the strategy
	 */
	private function __construct () {
	
		if (ApineRequest::is_api_call()) {
			$this->strategy = new ApineAPIRouter();
		} else {
			$this->strategy = new ApineWebRouter();
		}
	
	}
	
	/**
	 * Singleton design pattern implementation
	 *
	 * @return ApineSession
	 */
	public static function get_instance () {
	
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
	
		return self::$_instance;
	
	}
	
	/**
	 * Route the request to the best matching controller and action
	 *
	 * @param string $request
	 * @return ApineRoute
	 */
	public static function route ($request) {
	
		return self::get_instance()->strategy->route($request);
	
	}
	
	/**
	 * Execute an action
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 */
	public static function execute ($controller, $action, $args = null) {
	
		return self::get_instance()->strategy->execute($controller, $action, $args);
	
	}
	
	/**
	 * Returns the route manager
	 *
	 * @return ApineRouterInterface
	 */
	public static function get_handler () {
	
		return self::get_instance()->strategy;
	
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