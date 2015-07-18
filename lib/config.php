<?php

class Config{
	
	private static $_instance;
	private $settings = [];
	
	private function __construct(){
		if(file_exists('config.ini')){
			$this->settings = parse_ini_file('config.ini', true);
		}else{
			die("No config file founded.");
		}
	}
	
	public static function get_instance(){
		if(!isset(self::$_instance)){
			self::$_instance = new static();
		}
		return self::$_instance;
	}
	
	public static function get($prefix, $key){
		$prefix = strtolower($prefix);
		$key = strtolower($key);
		return isset(self::get_instance()->settings[$prefix][$key]) ? self::get_instance()->settings[$prefix][$key] : null;
	}
}