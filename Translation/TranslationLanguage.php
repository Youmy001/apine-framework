<?php
/**
 * Translation Language
 * This script contains a representation of languages for Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Translation;

/**
 * Translation Language
 * Representation of a language for translations
 */
final class TranslationLanguage {
	
	/**
	 * Full language ISO code
	 * 
	 * @var string
	 */
	public $code;
	
	/**
	 * Short two chars language code
	 * 
	 * @var string
	 */
	public $code_short;
	
	/**
	 * Path to the language file
	 * 
	 * @var string
	 */
	public $file_path;
	
	/**
	 * Instantiate the translation language
	 * 
	 * @param string $a_code
	 * @param string $a_file_path
	 */
	public function __construct ($a_code = null, $a_file_path = null) {
		
		if (is_string($a_code) && is_string($a_file_path)) {
			$this->code = $a_code;
			$this->code_short = substr($a_code, 0, 2);
			$this->file_path = $a_file_path;
		}
		
	}

}