<?php
/**
 * Module Autoloader
 * This script contains a loading helper to load many files at once
 *  
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
require_once ('lib/core/utils.php');

/**
 * Module Files Loading Tool
 * 
 * Tools to load files in batches from various locations in the project's directory
 */
final class ApineAutoload {
	
	/**
	 * Loads all files recursively of a user defined module in the modules/ directory
	 * 
	 * @param string $module_name 
	 * 			Name of the folder of the module
	 * @return boolean
	 */
	static function load_module ($module_name) {
		
		if (is_dir('modules/' . $module_name . '/')) {
			$dir = 'modules/' . $module_name . '/';
			$files = self::get_folder_files($dir);
			
			try {
				foreach ($files as $file) {
					if (file_extension($file) === "php") {
						require_once $file;
					}
				}
				
				return true;
			} catch (Exception $e) {
				return false;
			}
		} else if (is_file('modules/'.$module_name)) {
			if (file_extension('modules/' . $module_name) === "php") {
				require_once 'modules/'.$module_name;
			}
			
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Loads all files part of the frameworks kernel
	 * 
	 * @return boolean
	 */
	static function load_kernel() {
		
		$files=self::get_folder_files('lib/');
		
		try {
			foreach ($files as $file) {
				//print "$file\r\n";
				if (file_extension($file) === "php") {
					require_once $file;
				}
			}
			
			return true;
		} catch(Exception $e) {
			return false;
		}
		
	}
	
	/**
	 * Returns a recursive list of all files in a directory and its sub-directories
	 * 
	 * @param string $directory
	 * 			Path to the directory from the include path
	 * @param boolean $root
	 * 			Weither the directory is the base folder for the recursive parser.
	 * @return mixed[] List of all files in a directory
	 */
	public static function get_folder_files ($directory, $root = true) {
		
		$array = array();
		
		if (is_dir($directory)) {
			
			if (!$root) {
				
				// Extract directories and files
				$a_dir = array();
				$a_file = array();
				
				foreach (scandir($directory) as $file) {
					if ($file != "." && $file != "..") {
						if (is_dir($directory . $file . '/')) {
							$a_dir[] = $directory . $file;
						} else {
							$a_file[] = $directory . $file;
						}
					}
				}
					
				// Run sub-directories first
				foreach ($a_dir as $file) {
					if ($file != "." && $file != "..") {
						$directory_array=self::get_folder_files($file . '/', false);
							
						foreach ($directory_array as $directory_file) {
							$array[] = $directory_file;
						}
					}
				}
					
				// Then files
				foreach ($a_file as $file) {
					$array[] = $file;
				}
			} else {
				
				foreach (scandir($directory) as $file) {
					if ($file != "." && $file != "..") {
						if (is_dir($directory . $file . '/')) {
							$directory_array = self::get_folder_files($directory . $file . '/', false);
		
							foreach ($directory_array as $directory_file) {
								$array[] = $directory_file;
							}
						} else {
							$array[] = $directory.$file;
						}
					}
				}
			}
		}else{
			return null;
		}
		
		return $array;
		
	}
	
}
