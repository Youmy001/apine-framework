<?php

class Config{
	
	private static $_instance;
	//private $settings = [];
	private $settings;
	
	private function __construct() {
		
		if (file_exists('config.ini')) {
			$this->settings = parse_ini_file('config.ini', true);
		} else {
			die("No config file founded.");
		}
		
	}
	
	public static function get_instance() {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	public static function get($prefix, $key) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		return isset(self::get_instance()->settings[$prefix][$key]) ? self::get_instance()->settings[$prefix][$key] : null;
		
	}
	
	public static function set($prefix, $key, $value) {
		
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		
		self::get_instance()->settings[$prefix][$key]=$value;
		write_ini_file(self::get_instance()->settings, 'config.ini',true);
		
	}
}

/**
 * Source: http://stackoverflow.com/questions/1268378/create-ini-file-write-values-in-php
 */
function write_ini_file($assoc_arr, $path, $has_sections = FALSE) {

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