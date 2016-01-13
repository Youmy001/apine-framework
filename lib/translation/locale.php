<?php
/**
 * Translation Locale
 * This script contains a representation of locales for Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Translation Locales
 * Representation of a locales for translations
 */
final class ApineTranslationLocale {
	
	/**
	 * Entries linke to locales
	 * 
	 * @var string[]
	 */
	//private $locale_entries = [];
	private $locale_entries;
	
	/**
	 * Timezone name
	 * 
	 * @var string
	 */
	private $timezone;
	
	/**
	 * Timezone offset
	 * 
	 * @var integer
	 */
	private $offset;
	
	/**
	 * Formatted offset
	 * 
	 * @var string
	 */
	private $iso_offset;
	
	/**
	 * Locales related Language
	 * 
	 * @var TranslationLanguage
	 */
	private $language;
	
	/**
	 * Instantiate the translation language
	 *
	 * @param ApineTranslationLanguage $a_language
	 * @throws ApineException If the file is inexistant or invalid
	 */
	public function __construct(ApineTranslationLanguage $a_language) {
		
		$this->locale_string = array();
		$this->language = $a_language;
		
		if (file_exists($this->language->file_path)) {
			$file = new ApineFile($this->language->file_path, true);
				
			if($file->extention() == "json"){
				$content = $file->content();
				$content = json_decode($content);
				$array = array();
				$indexes = array();
		
				foreach ($content as $part => $sub_content) {
					$indexes[$part] = $sub_content;
				}
				
				if (isset($indexes['locale'])) {
					foreach ($indexes['locale'] as $key => $value) {
						$array[$key] = $value;
					}
				} else {
					$array['datehour'] = ApineConfig::get('dateformat', "datehour");
					$array['date'] = ApineConfig::get('dateformat', "date");
					$array['hour'] = ApineConfig::get('dateformat', "hour");
					$array['year'] = ApineConfig::get('dateformat', "year");
				}
		
				$this->locale_entries = $array;
			}else{
				throw new ApineException("Invalid File");
			}
		}else{
			throw new ApineException("Inexistant File");
		}
		
	}
	
	/**
	 * Returns date and time format string
	 * 
	 * @return string
	 */
	public function datehour () {
		
		return $this->locale_entries['datehour'];
		
	}
	
	/**
	 * Returns time format string
	 * 
	 * @return string
	 */
	public function hour () {
		
		return $this->locale_entries['hour'];
		
	}
	
	/**
	 * Returns date format string
	 *
	 * @return string
	 */
	public function date () {
		
		return $this->locale_entries['date'];
		
	}
	
	/*public function format ($locale_str, $string) {
		
	}*/
}
