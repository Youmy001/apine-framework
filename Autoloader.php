<?php
/**
 * Module Autoloader
 * This script contains a loading helper to load many files at once
 *  
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine;

//use \Exception;

require_once __DIR__ . '/Core/utils.php';

//$before = microtime(true) * 1000;
apine_execution_time();

/**
 * Module Files Loading Tool
 * 
 * Tools to load files in batches from various locations in the project's directory
 */
final class Autoloader {
	
	/**
	 * An associative array where the key is a namespace prefix and the value
	 * is an array of base directories for classes in that namespace.
	 *
	 * @var array
	 */
	protected $prefixes = array();
	
	public function __construct() {
		
		//$apine_folder = basename(dirname(__FILE__));
		$apine_folder = __DIR__;
		
		$this->add_module('Apine', $apine_folder);
		$this->add_module('Apine\Modules', 'modules');
		$this->add_module('Apine\Controllers\User', 'controllers');
		$this->add_module('Apine\Controllers\System', $apine_folder . '/Controllers');
		
	}
	
	/**
	 * Adds a base directory for a namespace prefix.
	 *
	 * @param string $prefix The namespace prefix.
	 * @param string $base_dir A base directory for class files in the
	 * namespace.
	 * @param bool $prepend If true, prepend the base directory to the stack
	 * instead of appending it; this causes it to be searched first rather
	 * than last.
	 * @return void
	 */
	public function add_module ($prefix, $base_dir, $prepend = false) {
		
		// normalize namespace prefix
		$prefix = trim($prefix, '\\') . '\\';
	
		// normalize the base directory with a trailing separator
		$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';
	
		// initialize the namespace prefix array
		if (isset($this->prefixes[$prefix]) === false) {
			$this->prefixes[$prefix] = array();
		}
	
		// retain the base directory for the namespace prefix
		if ($prepend) {
			array_unshift($this->prefixes[$prefix], $base_dir);
		} else {
			array_push($this->prefixes[$prefix], $base_dir);
		}
		
	}
	
	/**
	 * Loads all files recursively of a user defined module in the modules/ directory
	 * By default this autoloader reserves the namespace Apine\Modules for anything in the /modules
	 * directory.
	 * 
	 * An autoloader.php may be used to register classes in that directory. The autoloader.php file
	 * should either add a namespace to this autoloader in the following fashion :
	 * 
	 *     $autoloader->add_module('Custom\Namespace', __DIR__);
	 *     
	 * Or contain a spl_autoload_register function. 
	 * 
	 * @param string $module_name 
	 * 			Name of the folder of the module
	 * @return boolean
	 */
	static function load_module ($module_name) {
		
		// Verify if the module actually exists
		if (is_dir('modules/' . $module_name . '/')) {
			$dir = 'modules/' . $module_name . '/';
			$autoloader = &$this;
			
			// Load the autoloader if it exists.
			// Load recursively the directory if not.
			if (file_exists($dir . 'Autoloader.php')) {
				require $dir . 'Autoloader.php';
			} else if (file_exists($dir . 'autoloader.php')) {
				require $dir . 'autoloader.php';
			} else {
				$files = self::get_folder_files($dir);
				
				try {
					foreach ($files as $file) {
						if (file_extension($file) === "php") {
							require_once $file;
						}
					}
					
					return true;
				} catch (\Exception $e) {
					return false;
				}
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
	
	public function register ()  {
		
		spl_autoload_register(array($this, 'load_class'));
		
	}
	
	/**
	 * Loads the class file for a given class name.
	 *
	 * @param string $class The fully-qualified class name.
	 * @return mixed The mapped file name on success, or boolean false on
	 * failure.
	 */
	public function load_class ($class) {
		
		// the current namespace prefix
		$prefix = $class;
	
		// work backwards through the namespace names of the fully-qualified
		// class name to find a mapped file name
		while (false !== $pos = strrpos($prefix, '\\')) {
	
			// retain the trailing namespace separator in the prefix
			$prefix = substr($class, 0, $pos + 1);
	
			// the rest is the relative class name
			$relative_class = substr($class, $pos + 1);
	
			// try to load a mapped file for the prefix and relative class
			$mapped_file = $this->load_mapped_file($prefix, $relative_class);
			
			if ($mapped_file) {
				return $mapped_file;
			}
	
			// remove the trailing namespace separator for the next iteration
			// of strrpos()
			$prefix = rtrim($prefix, '\\');
		}
	
		// never found a mapped file
		return false;
		
	}
	
	/**
	 * Load the mapped file for a namespace prefix and relative class.
	 *
	 * @param string $prefix The namespace prefix.
	 * @param string $relative_class The relative class name.
	 * @return mixed Boolean false if no mapped file can be loaded, or the
	 * name of the mapped file that was loaded.
	 */
	protected function load_mapped_file ($prefix, $relative_class) {
		
		// are there any base directories for this namespace prefix?
		if (isset($this->prefixes[$prefix]) === false) {
			return false;
		}
	
		// look through base directories for this namespace prefix
		foreach ($this->prefixes[$prefix] as $base_dir) {
	
			// replace the namespace prefix with the base directory,
			// replace namespace separators with directory separators
			// in the relative class name, append with .php
			$file = $base_dir
			. str_replace('\\', '/', $relative_class)
			. '.php';
	
			// if the mapped file exists, require it
			if ($this->require_file($file)) {
				// yes, we're done
				return $file;
			}
		}
	
		// never found it
		return false;
		
	}
	
	/**
	 * If a file exists, require it from the file system.
	 *
	 * @param string $file The file to require.
	 * @return bool True if the file exists, false if not.
	 */
	protected function require_file ($file) {
		
		if (file_exists($file)) {
			require_once $file;
			return true;
		}
		
		return false;
		
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
	private static function get_folder_files ($directory, $root = true) {
		
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
	
	/**
	 * Load recursively all files in a directory and its sub-directories
	 *
	 * @param string $a_folder
	 * 			Path to the directory from the include path
	 */
	private function load_folder_files ($a_folder) {
		
		$files = glob($a_folder . '*');
		
		foreach ($files as $item) {
			if (is_dir($item)) {
				self::load_folder_files($item . "/");
			}
		}
		
		foreach ($files as $item) {
			if (!is_dir($item) && file_extension($item) == 'php') {
				require_once $item;
			}
		}
		
	}
	
}
