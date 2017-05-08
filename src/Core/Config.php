<?php
/**
 * Configuration Reader
 * This script contains an helper to read configuration files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

use Apine\Exception\GenericException;
use Apine\Utility\Files;

/**
 * Configuration Reader
 * 
 * Read and write project's configuration file
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Config {
	
	/**
	 * Path to the config file
	 * 
	 * @var string
	 */
	private $path;
	
	/**
	 * Setting strings for the file
	 *
	 * @var object
	 */
	private $settings;
	
	/**
	 * Construct the Conguration Reader handler
	 * 
	 * Extract string from the configuration file
     *
     * @param string $a_path
     * @throws GenericException If file not found
	 */
	public function __construct ($a_path) {
		
		try {
			$this->path = $a_path;
			$this->settings = JsonStore::get($a_path);
		} catch (\Exception $e) {
			throw new GenericException("Config file not found.", 500, $e);
		}
		
	}
	
	/**
	 * @param $name
	 *
	 * @return mixed
	 *
	 */
	public function &__get ($name) {
		
		return $this->settings->settings->$name;
		
	}
	
	/**
	 * @param $name
	 * @param $value
	 */
	public function __set ($name, $value) {
		
		$this->settings->settings->$name = $value;
		
	}
}