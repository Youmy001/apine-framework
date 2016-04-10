<?php
/**
 * Versioning validator
 *  
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

use Apine\Core\ApplicationConfig;
use Apine\File\File;

/**
 * Verifies version numbers for modules
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class OldVersion {
	
	/**
	 * Regular expression for a valid semantic version number
	 * 
	 * @var string
	 */
	const VERSION_REGEX = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/';
	
	/**
	 * Check a module version number
	 * 
	 * @param string $module_name Name of the module
	 * @return string
	 */
	public static function module ($module_name) {
		
		if (is_file('modules/' . $module_name . '/VERSION')) {
			$file = new File('modules/' . $module_name . '/VERSION', true);
			$version = $file->content();
		} else if (is_file($module_name. '/VERSION')) {
			$file = new File($module_name . '/VERSION', true);
			$version = $file->content();
		}
		
		if(isset($version) && self::validate($version)) {
			return $version;
		} else {
			return self::application();
		}
		
	}
	
	/**
	 * Check application version number
	 * 
	 * @return string
	 */
	public static function application () {
		
		if (is_file('VERSION')) {
			$file = new File('VERSION', true);
			$version = $file->content();
		} else if (ApplicationConfig::get('application', 'version')) {
			$version = ApplicationConfig::get('application', 'version');
		}
		
		if (isset($version) && self::validate($version)) {
			return $version;
		} else {
			return self::framework();
		}
		
	}
	
	/**
	 * Check framework version number
	 * 
	 * @return string
	 */
	public static function framework () {
		
		$version = apine_application()->get_version();
		
		/*if (!$version) {
			$folder = realpath(dirname(__FILE__) . '/..');
			
			if (is_file($folder . '/VERSION')) {
				$file = new ApineFile($folder . '/VERSION', true);
				$file_version = $file->content();
			} else if (ApineAppConfig::get('apine-framework', 'version')) {
				$file_version = ApineAppConfig::get('apine-framework', 'version');
			}
			
			$version = (isset($file_version) && self::validate($file_version)) ? $file_version : null;
		}*/
		
		return $version;
	}
	
	/**
	 * Validate version number
	 * 
	 * @param string $version
	 * @return boolean
	 */
	public static function validate ($version) {
		
		return (true == preg_match(self::VERSION_REGEX, $version));
		
	}
	
}
