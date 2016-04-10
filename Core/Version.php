<?php
/**
 * Versioning validator
 *  
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\Core;

use Apine\File\File;
use Apine\Exception\GenericException;

/**
 * Verifies version numbers for modules
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class Version {
	
	/**
	 * Regular expression for a valid semantic version number
	 * 
	 * @var string
	 */
	const VERSION_REGEX = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/';
	
	private $framework;
	
	private $application;
	
	public function __construct($framework_version, $application_version = null) {
		
		if (empty($application_version)) {
			if (is_file('VERSION')) {
				$file = new File('VERSION', true);
				$application_version = $file->content();
			} else {
				$application_version = $framework_version;
			}
		}
		
		if (self::validate($framework_version) && self::validate($application_version)) {
			$this->application = $application_version;
			$this->framework = $framework_version;
		} else {
			throw new GenericException('Invalid Version Numbers', 500);
		}
		
	}
	
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
	public function application () {
		
		return $this->application;
		
	}
	
	/**
	 * Check framework version number
	 * 
	 * @return string
	 */
	public function framework () {
		
		return $this->framework;
		
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
