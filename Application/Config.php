<?php
/**
 * Configuration Reader
 * This script contains an helper to read configuration files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Application;

/**
 * Configuration Reader
 * 
 * Read and write project's configuration file
 */
final class Config {
	
	/**
	 * Instance of the Config reader
	 * Singleton Implementation
	 * 
	 * @var ApplicationConfig
	 */
	private static $_instance;
	
	
	/**
	 * Setting strings extracted from the configuration file
	 * 
	 * @var array
	 */
	private $settings;
	//private $settings = [];
	
	/**
	 * Construct the Conguration Reader handler
	 * 
	 * Extract string from the configuration file 
	 */
	private function __construct () {
		
		$this->settings = Application::get_instance()->get_config()->get_config();
		
	}
	
	/**
	 * Singleton design pattern implementation
	 * 
	 * @static
	 * @return ApineConfig
	 */
	public static function get_instance () {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	/**
	 * Fetch a configuration string
	 * 
	 * @param string $prefix
	 * @param string $key
	 * @return string
	 */
	public static function get ($prefix, $key) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		return isset(self::get_instance()->settings[$prefix][$key]) ? self::get_instance()->settings[$prefix][$key] : null;
		
	}
	
	/**
	 * Fetch all configuration strings
	 * 
	 * @return array
	 */
	public static function get_config () {
		
		return self::get_instance()->settings;
		
	}
	
	/**
	 * Write or update a configuration string
	 * 
	 * @param string $prefix
	 * @param string $key
	 * @param string $value
	 */
	public static function set ($prefix, $key, $value) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		
		self::get_instance()->settings[$prefix][$key] = $value;
		//write_ini_file(self::get_instance()->settings, 'config.ini', true);
        
        // Update the parent and the file
        Application::get_instance()->get_config()->set($prefix, $key, $value);
		
	}
}