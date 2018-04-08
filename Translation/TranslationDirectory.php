<?php
/**
 * Translation Tool
 * This script contains a facade for the Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Translation;

use Apine\Application\Application;
use Apine\Core\Config;

/**
 * Language Finder Tool
 * Manage multi-language configuration
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Translation
 */
final class TranslationDirectory {
	
	/**
	 * Available languages for translations
	 * 
	 * @var TranslationLanguage[]
	 */
	private static $languages;
	
	/**
	 * Translations for available languages
	 * 
	 * @var Translation[]
	 */
	private static $translations;
	
	/**
	 * Language Directory
	 * 
	 * @var string $directory
	 */
	private $directory;
	
	/**
	 * Construct the Translator handler
	 * Finding available language from language files
	 * 
	 * @param string $a_directory
     * @throws \InvalidArgumentException If the directory is not found
	 */
    public function __construct ($a_directory = '') {
    
        if (is_null(self::$languages) && is_null(self::$translations)) {
            self::$languages = array();
            self::$translations = array();
        }
    
        $config = Application::get_instance()->get_config();
    
        if (null === ($default_directory = $config->get('localization','locale_directory'))) {
            $default_directory = 'resources/languages';
        }
    
        $directory = (empty($a_directory)) ? $default_directory : $a_directory;
        
        if (strpos('/', $directory) === 0) {
            $directory = '.' . $directory;
        }
        
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf("Locale directory %s not found.", $directory));
        }
    
        $this->directory = $directory;
    
        if(!isset(self::$languages[$this->directory])) {
    
            if (preg_match('/[^\/]$/', $directory) === 1) {
                $path = $directory . '/';
            }
        
            // Find and instantiate every languages
            foreach (scandir($directory) as $file) {
                if ($file != "." && $file != "..") {
                    $file_name = explode(".", $file);
                    $file_name = $file_name[0];
                
                    if (is_file($path . $file)) {
                        if ((preg_match('/^[a-z]{2}(_([a-zA-Z]{2}){1,2})?_[A-Z]{2}$/', $file_name) === 1) ||
                            (preg_match('/^[a-z]{2}(-([a-zA-Z]{2}){1,2})?-[A-Z]{2}$/', $file_name) === 1)) {
                            self::$languages[$this->directory][$file_name] = new TranslationLanguage($file_name,
                                $path . $file);
                        }
                    }
                }
            }
        }
		
	}
	
	/**
	 * Fetch a list of every translation language available 
	 * 
	 * @return TranslationLanguage[]
	 */
	public function get_all_languages () {
		
		// Return a list of every language
		return self::$languages[$this->directory];
		
	}
	
	/**
	 * Fetch an available translation language
	 * 
	 * @param string $a_language_code
	 * @return TranslationLanguage
	 */
	public function get_language ($a_language_code) {
		
		// Return a language whose code matches the parameter
		return isset(self::$languages[$this->directory][$a_language_code]) ? self::$languages[$this->directory][$a_language_code] : null;
		
	}
	
	/**
	 * Verify if a language is available
	 * 
	 * @param string $a_language_code
	 * @return <boolean|string>
	 */
	public function is_exist_language ($a_language_code) {
		
		// Verify if a language matches the parameter
		if (isset(self::$languages[$this->directory][$a_language_code])) {
			$return = $a_language_code;
		} else {
			if (strlen($a_language_code) > 2) {
				$a_language_code = substr($a_language_code, 0, 2);
			}
			
			$matches = array();
			$translations = array();
			
			foreach (self::$languages[$this->directory] as $item) {
				if($item->code_short == $a_language_code) {
					$translation = new Translation($item);
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
	 * @return Translation
	 */
	public function get_translation ($a_language_code) {
		
		// Load and return translation string of a language whose code matches the parameter
		if (isset(self::$languages[$this->directory][$a_language_code])) {
			if(!isset(self::$translations[$this->directory][$a_language_code])) {
				self::$translations[$this->directory][$a_language_code] = new Translation(self::$languages[$this->directory][$a_language_code]);
			}
			
			return self::$translations[$this->directory][$a_language_code];
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
	public function translate ($a_language_code, $a_key, $a_index = null, $a_pattern = null) {
		
		// Fetch a translation string of a matching language whose key and index matches parameters
		// and return a formatted version from a pattern is provided
		$translation = $this->get_translation($a_language_code);
		
		if ($translation != null) {
			return $translation->parse($a_key, $a_index, $a_pattern);
		} else {
			return null;
		}
		
	}
	
}