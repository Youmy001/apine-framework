<?php
/**
 * Langauage Translation
 * This script contains a representation the translation of a language for Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

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
	 * @throws Exception If the file is inexistant or invalid
	 */
	public function __construct (TranslationLanguage $a_language) {
		
		$this->language = $a_language;
		$this->locale = new TranslationLocale($this->language);
		
		if (file_exists($this->language->file_path)) {
			$file = new ApineFile($this->language->file_path, true);
			
			if($file->extention() == "json"){
				$content = $file->content();
				$content = json_decode($content);
				$array = array();
				
				foreach ($content as $part => $sub_content) {
					if (is_string($sub_content)){
						$array[$part] = $sub_content;
					} else {
						foreach ($sub_content as $key => $value) {
							$array[$part][$key] = $value;
						}
					}
				}
				
				$this->entries = $array;
			}else{
				throw new Exception("Invalid File");
			}
		}else{
			throw new Exception("Inexistant File");
		}
		
	}
	
	/**
	 * Fetch a translation string
	 * 
	 * @param string $a_prefix
	 * @param string $a_key
	 * @return string
	 */
	public function get ($a_prefix, $a_key = null) {
		
		$prefix = strtolower($a_prefix);
		
		if ($a_key != null) {
			$key = strtolower($a_key);
			return isset($this->entries[$prefix][$key]) ? $this->entries[$prefix][$key] : null;
		} else {
			return isset($this->entries[$prefix]) ? $this->entries[$prefix] : null;
		}
		
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
	 * @return TranslationLanguage
	 */
	public function get_language () {
		
		return $this->language;
		
	}
	
	public function get_locale () {
		
		return $this->locale;
	}
	
}