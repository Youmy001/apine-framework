<?php
/**
 * Configuration Reader
 * This script contains an helper to read configuration files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Configuration Reader
 * Read and write project's configuration file
 */
class Config {
	
	/**
	 * Instance of the Config reader
	 * Singleton Implementation
	 * 
	 * @var Config
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
	 * Extract string from the configuration file 
	 */
	private function __construct () {
		
		if (file_exists('config.ini')) {
			$this->settings = parse_ini_file('config.ini', true);
		} else {
			die("No config file founded.");
		}
		
	}
	
	/**
	 * Singleton design pattern implementation
	 * 
	 * @static
	 * @return Config
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
	 * @return <NULL|array>
	 */
	public static function get ($prefix, $key) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		return isset(self::get_instance()->settings[$prefix][$key]) ? self::get_instance()->settings[$prefix][$key] : null;
		
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
		write_ini_file(self::get_instance()->settings, 'config.ini', true);
		
	}
}

/**
 * Write strings in a configuration file in INI format
 * Source: http://stackoverflow.com/questions/1268378/create-ini-file-write-values-in-php
 */
function write_ini_file ($assoc_arr, $path, $has_sections = FALSE) {

	$content = "";
	
	if ($has_sections) {
		foreach ($assoc_arr as $key=>$elem) {
			$content .= "[" . $key . "]\n";
			
			foreach ($elem as $key2=>$elem2) {
				if (is_array($elem2)) {
					for ($i = 0;$i < count($elem2);$i++) {
						$content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
					}
				} else if($elem2 == "") {
					$content .= $key2 . " = \n";
				} else {
					$content .= $key2 . " = \"" . $elem2 . "\"\n";
				}
			}
		}
	} else {
		foreach ($assoc_arr as $key=>$elem) {
			if (is_array($elem)) {
				for ($i = 0;$i < count($elem);$i++) {
					$content .= $key . "[] = \"" . $elem[$i] . "\"\n";
				}
			} else if($elem == "") {
				$content .= $key . " = \n";
			} else {
				$content .= $key . " = \"" . $elem . "\"\n";
			}
		}
	}
	
	if (!$handle = fopen($path, 'w')) {
		return false;
	}
	
	$success = fwrite($handle, $content);
	fclose($handle);
	return $success;

}