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
class TranslationLocale {
	
	//private $locale_entries = [];
	private $locale_entries;
	
	private $timezone;
	
	private $offset;
	
	private $iso_offset;
	
	private $language;
	
	public function __construct(TranslationLanguage $a_language) {
		
		$this->locale_string = array();
		$this->language = $a_language;
		
		if (file_exists($this->language->file_path)) {
			$file = new ApineFile($this->language->file_path);
				
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
					$array['timezone'] = Config::get('dateformat', "timezone");
					$array['datehour'] = Config::get('dateformat', "datehour");
					$array['date'] = Config::get('dateformat', "date");
					$array['hour'] = Config::get('dateformat', "hour");
					$array['year'] = Config::get('dateformat', "year");
				}
		
				$this->locale_entries = $array;
			}else{
				throw new Exception("Invalid File");
			}
		}else{
			throw new Exception("Inexistant File");
		}
		
	}
	
	public function timezone () {
		
		return $this->locale_entries['timezone'];
		
	}
	
	public function offset () {
		
		if(is_null($this->offset)) {
			$datetime = new DateTime($this->locale_entries['timezone']);
			$this->offset = $datetime->getOffset();
		}
		
		return $this->offset;
	}
	
	public function iso_offset () {
	
		if(is_null($this->iso_offset)) {
			$datetime = new DateTime($this->locale_entries['timezone']);
			$hours = intval($datetime->getOffset()/3600);
			$minutes = sprintf("%02d", strstr($datetime->getOffset()/3600,".")*60);
				
			if ($hours > 0) {
				$hours = "+".sprintf("%02d", $hours);
			} else {
				$hours = sprintf("%03d", $hours);
			}
				
			$this->iso_offset = $hours . ":" . $minutes;
		}
		
		return $this->iso_offset;
	}
	
	public function datehour () {
		
		return $this->locale_entries['datehour'];
		
	}
	
	public function hour () {
		
		return $this->locale_entries['hour'];
		
	}
	
	public function date () {
		
		return $this->locale_entries['date'];
		
	}
	
	/*public function format ($locale_str, $string) {
		
	}*/
}