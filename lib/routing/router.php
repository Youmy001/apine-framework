<?php

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
	
	private function __construct () {
	
		if (Request::is_api_call()) {
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
	 * @return ApineRoute
	 */
	public static function route () {
	
		return self::get_instance()->strategy->route();
	
	}
	
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
	
	public function route ();
	
	public function execute ($controller, $action, $args);
	
}