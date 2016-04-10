<?php
/**
 * Configuration Reader
 * This script contains an helper to read configuration files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

/**
 * Configuration Reader
 * 
 * Read and write project's configuration file
 */
final class Config {
	
	/**
	 * Path to the config file
	 * 
	 * @var string
	 */
	private $path;
	
	
	/**
	 * Setting strings extracted from the configuration file
	 * 
	 * @var array
	 */
	//private $settings;
	private $settings = [];
	
	/**
	 * Construct the Conguration Reader handler
	 * 
	 * Extract string from the configuration file 
	 */
	public function __construct ($a_path = 'config.ini') {
		
		if (file_exists($a_path)) {
			$this->path = $a_path;
			$this->settings = parse_ini_file($a_path, true);
		} else {
			throw new \Apine\Exception\GenericException("Config file not found.", 500);
		}
		
	}
	
	/**
	 * Fetch a configuration string
	 * 
	 * @param string $prefix
	 * @param string $key
	 * @return string
	 */
	public function get ($prefix, $key) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		return isset($this->settings[$prefix][$key]) ? $this->settings[$prefix][$key] : null;
		
	}
	
	/**
	 * Fetch all configuration strings
	 * 
	 * @return array
	 */
	public function get_config () {
		
		return $this->settings;
		
	}
	
	/**
	 * Write or update a configuration string
	 * 
	 * @param string $prefix
	 * @param string $key
	 * @param string $value
	 */
	public function set ($prefix, $key, $value) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		
		$this->settings[$prefix][$key] = $value;
		write_ini_file($this->settings, $this->path, true);
		
	}
}