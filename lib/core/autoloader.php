<?php

function load_module($module_name){
	Autoload::load_module($module_name);
}

class Autoload{
	
	static function load_module($module_name){
		$dir='model/'.$module_name.'/';
		$files=self::get_folder_files($dir);
		
		foreach ($files as $file){
			require_once $file;
		}
	}
	
	static function load_kernel(){
		$files=self::get_folder_files('lib/');
		
		foreach ($files as $file){
			require_once $file;
		}
	}
	
	private static function get_folder_files($directory,$root=true){
		$array=array();
		if (is_dir($directory)) {
			if(!$root){
				// Extract directories and files
				$a_dir=array();
				$a_file=array();
				foreach(scandir($directory) as $file){
					if ($file != "." && $file != "..") {
						if(is_dir($directory . $file . '/')){
							$a_dir[]=$directory.$file;
						}else{
							$a_file[]=$directory.$file;
						}
					}
				}
					
				// Run sub-directories first
				foreach ($a_dir as $file){
					if ($file != "." && $file != "..") {
						$directory_array=self::get_folder_files($file . '/',false);
							
						foreach ($directory_array as $directory_file){
							$array[]=$directory_file;
						}
					}
				}
					
				// Then files
				foreach ($a_file as $file){
					$array[]=$file;
				}
					
			}else{
				foreach(scandir($directory) as $file){
					if ($file != "." && $file != "..") {
						if(is_dir($directory . $file . '/')){
							$directory_array=self::get_folder_files($directory . $file . '/',false);
		
							foreach ($directory_array as $directory_file){
								$array[]=$directory_file;
							}
						}else{
		
							$array[]=$directory.$file;
						}
					}
				}
			}
		}
		return $array;
	}
}