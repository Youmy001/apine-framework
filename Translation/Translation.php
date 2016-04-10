<?php
/**
 * Langauage Translation
 * This script contains a representation the translation of a language for Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Translation;

use Apine\Exception\GenericException;

/**
 * Language Translation
 * Representation of a translations for a language
 */
final class Translation {

	/**
	 * Representation of a language for translation
	 * 
	 * @var TranslationLanguage
	 */
	private $language;
	
	/**
	 * Representation of a language for translation
	 *
	 * @var TranslationLocale
	 */
	private $locale;
	
	/**
	 * Translation strings extracted from the translation file
	 * 
	 * @var array
	 */
	private $entries;
	
	/**
	 * Construct the Translation handler
	 * Extract string from the translation file
	 * 
	 * @param TranslationLanguage $a_language
	 * @throws ApineException If the file is inexistant or invalid
	 */
	public function __construct (TranslationLanguage $a_language) {
		
		$this->language = $a_language;
		$this->locale = new TranslationLocale($this->language);
		
		if (file_exists($this->language->file_path)) {
			if (file_extension($this->language->file_path) != "json") {
				throw new GenericException("Invalid File");
			}
		}else{
			throw new GenericException("Inexistant File");
		}
		
	}
	
	private function load_file () {
		
		$file = fopen($this->language->file_path, 'r');
		$content = fread($file, filesize($this->language->file_path));
		$content = json_decode($content);

		$this->entries = $content;
		
	}
	
	/**
	 * Fetch a translation string
	 * 
	 * @param string $a_prefix
	 * @param string $a_key
	 * @return string
	 */
	public function get ($a_prefix, $a_key = null) {
		
		if (is_null($this->entries)) {
			$this->load_file();
		}
		
		$prefix = strtolower($a_prefix);
		
		if ($a_key != null) {
			$key = strtolower($a_key);
			return isset($this->entries->$prefix->$key) ? $this->entries->$prefix->$key : null;
		} else {
			return isset($this->entries->$prefix) ? $this->entries->$prefix : null;
		}
		
	}
	
	public function get_all () {
		
		if (is_null($this->entries)) {
			$this->load_file();
		}
		
		return $this->entries;
		
	}
	
	/**
	 * Fetch a translation string with a format
	 * 
	 * @param string $a_key
	 * @param string $a_index
	 * @param string $a_pattern
	 * @return string
	 */
	public function parse ($a_key, $a_index = null, $a_pattern = null) {
		
		if (!is_array($a_pattern)) {
			$a_pattern = array($a_pattern);
		}
		
		return call_user_func_array('sprintf', array_merge(array(
						$this->get($a_key, $a_index)
		), $a_pattern));
		
	}
	
	/**
	 * Return the Translation Language
	 * 
	 * @return ApineTranslationLanguage
	 */
	public function get_language () {
		
		return $this->language;
		
	}
	
	/**
	 * Return the locale linked with the translation
	 * 
	 * @return ApineTranslationLocale
	 */
	public function get_locale () {
		
		return $this->locale;
	}
	
}