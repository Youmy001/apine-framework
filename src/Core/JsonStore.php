<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 17/01/14
 * Time: 2:16
 */

namespace Apine\Core;


class JsonStore {
	
	private static $_instance;
	
	/**
	 * @var array
	 */
	private $jsons;
	
	private static function get_instance () {
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
	}
	
	static function &get ($a_path) {
		
		if (file_exists($a_path)) {
			$real_path = realpath($a_path);
			
			if (!isset(self::get_instance()->jsons[$real_path])) {
				$json = json_decode(file_get_contents($real_path));
				
				if (json_last_error() !== JSON_ERROR_NONE) {
					throw new \ErrorException('Invalid JSON');
				}
				
				self::get_instance()->jsons[$real_path] = $json;
			}
			
			return self::get_instance()->jsons[$real_path];
		} else {
			throw new \ErrorException('File not found');
		}
	}
	
	function __destruct () {
		
		foreach (self::get_instance()->jsons as $file_path => $content) {
			//print "Save file $file_path;";
		}
		
	}
	
}