<?php
/**
 * Translator Tool
 * This script contains a facade for the Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Language Translator Tool
 * Manage multi-language configuration
 * 
 * @deprecated
 */
final class ApineTranslator {
	
	/**
	 * Availables languages for translations
	 * 
	 * @var ApineTranslationLanguage[]
	 */
	private $languages;
	
	/**
	 * Translations for available languages
	 * 
	 * @var ApineTranslation[]
	 */
	private $translations;
	
	/**
	 * Instance of the Translator
	 * Singleton Implementation
	 *
	 * @var ApineTranslator
	 */
	private static $_instance;
	
	/**
	 * Construct the Translator handler
	 * Finding available language from language files
	 */
	private function __construct (){
		
		$this->languages = array();
		$this->translations = array();
		
		//$array= [];
		$array = array();
		
		// Find and instantiate every languages
		foreach (scandir('resources/languages/') as $file) {
			if ($file != "." && $file != "..") {
				$file_name = explode(".", $file);
				$file_name = $file_name[0];
				
				if (is_file('resources/languages/' . $file)) {
					$this->languages[$file_name] = new ApineTranslationLanguage($file_name, 'resources/languages/' . $file);
				}
			}
		}
		
	}
	
	/**
	 * Singleton design pattern implementation
	 *
	 * @static
	 * @return ApineTranslator
	 */
	public static function get_instance () {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	/**
	 * Fetch a list of every translation language available 
	 * 
	 * @return ApineTranslationLanguage[]
	 */
	public static function get_all_languages () {
		
		// Return a liste of every language
		return self::get_instance()->languages;
		
	}
	
	/**
	 * Fetch an available translation language
	 * 
	 * @param string $a_language_code
	 * @return ApineTranslationLanguage
	 */
	public static function get_language ($a_language_code) {
		
		// Return a language whose code maches the parameter
		return isset(self::get_instance()->languages[$a_language_code]) ? self::get_instance()->languages[$a_language_code] : null;
		
	}
	
	/**
	 * Verify if a language is available
	 * 
	 * @param string $a_language_code
	 * @return boolean
	 */
	public static function is_exist_language ($a_language_code) {
		
		// Verify if a language matches the parameter
		if (isset(self::get_instance()->languages[$a_language_code])) {
			$return = $a_language_code;
		} else {
			if (strlen($a_language_code) > 2) {
				$a_language_code = substr($a_language_code, 0, 2);
			}
			
			$matches = array();
			$translations = array();
			
			foreach (self::get_instance()->languages as $item) {
				if($item->code_short == $a_language_code) {
					$translation = new ApineTranslation($item);
					$matches[$item->code] = $translation->get('language', 'priority');
					$translations[$item->code] = $translation;
				}
			}
			
			if (count($matches) == 1) {
				$found_language = reset($translations);
				$return = $found_language->get_language()->code;
			} else if (count($matches) > 1) {
				arsort($matches);
				$keys = array_keys($matches);
				$found_language = $translations[reset($keys)];
				$return = $found_language->get_language()->code;
			}
		}
		
		return (isset($return)) ? $return : false;
		
	}
	
	/**
	 * Fetch a translation
	 * 
	 * @param string $a_language_code
	 * @return ApineTranslation
	 */
	public static function get_translation ($a_language_code) {
		
		// Load and return translation string of a language whose code matches the parameter
		if (isset(self::get_instance()->languages[$a_language_code])) {
			if(!isset(self::get_instance()->translations[$a_language_code])) {
				self::get_instance()->translations[$a_language_code] = new ApineTranslation(self::get_instance()->languages[$a_language_code]);
			}
			
			return self::get_instance()->translations[$a_language_code];
		} else {
			return null;
		}
		
	}
	
	/**
	 * Fetch a translation string
	 * 
	 * @param string $a_language_code
	 * @param string $a_key
	 * @param string $a_index
	 * @param string $a_pattern
	 * @return string
	 */
	public static function translate ($a_language_code, $a_key, $a_index = null, $a_pattern = null) {
		
		// Fetch a translation string of a matching language whose key and index matches parameters
		// and return a formatted version from a pattern is provided
		$translation = self::get_instance()->get_translation($a_language_code);
		
		if ($translation != null) {
			return $translation->parse($a_key, $a_index, $a_pattern);
		} else {
			return null;
		}
		
	}
	
}