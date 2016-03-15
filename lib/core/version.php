<?php
/**
 * Versioning validator
 *  
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Verifies version numbers for modules
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class ApineVersion {
	
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
			$file = new ApineFile('modules/' . $module_name . '/VERSION', true);
			$version = $file->content();
		} else if (is_file($module_name. '/VERSION')) {
			$file = new ApineFile($module_name . '/VERSION', true);
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
			$file = new ApineFile('VERSION', true);
			$version = $file->content();
		} else if (ApineAppConfig::get('application', 'version')) {
			$version = ApineAppConfig::get('application', 'version');
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
		
		$folder = realpath(dirname(__FILE__) . '/..');
		
		if (is_file($folder . '/VERSION')) {
			$file = new ApineFile($folder . '/VERSION', true);
			$version = $file->content();
		} else if (ApineAppConfig::get('apine-framework', 'version')) {
			$version = ApineAppConfig::get('apine-framework', 'version');
		}
		
		return (isset($version) && self::validate($version)) ? $version : null;
	}
	
	/**
	 * Validate version number
	 * 
	 * @param string $version
	 * @return boolean
	 */
	private static function validate ($version) {
		
		return (true == preg_match(self::VERSION_REGEX, $version));
		
	}
	
}
