<?php
/**
 * Execution Translator
 * This script contains a facade for the Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Execution Translator
 * Manage Multi-Languages setups
 */
final class ApineAppTranslator {
	
	/**
	 * Instance of the Translation Manager
	 * Singleton Implementation
	 * 
	 * @var ApineAppTranslator
	 */
	private static $_instance;
	
	/**
	 * Current Language
	 * 
	 * @var ApineTranslation
	 */
	private $language;
	
	/**
	 * Singleton design pattern implementation
	 * @return ApineAppTranslator
	 */
	public static function get_instance () {
	
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
	
		return self::$_instance;
	
	}
	
	/**
	 * Set current session language
	 *
	 * @param string $a_lang_code
	 *        Language identifier
	 */
	public static function set_language ($a_lang_code = null) {
		
		$directory = new ApineTranslationDirectory();
		//$config = new ApineConfig('config.ini');
		
		if (is_null($a_lang_code)) {
			if (ApineAppConfig::get('languages', 'detection') == "yes") {
				if (!isset(ApineRequest::get()['language'])) {
					$language = self::cookie_best();
					
					if (!$language) {
						$language = self::user_agent_best();
					}
				} else {
					$language = self::request_best();
				}
				
				if (!$language) {
					$language = ApineAppConfig::get('languages', 'default');
				}
	
				self::get_instance()->language = $directory->get_translation($language);
			} else {
				if (isset(ApineRequest::get()['language'])) {
					$language = self::request_best();
				} else {
					$language = ApineAppConfig::get('languages', 'default');
				}
				
				self::get_instance()->language = $directory->get_translation($language);
			}
		} else {
			if (ApineTranslator::is_exist_language($a_lang_code)) {
				self::get_instance()->language = $directory->get_translation($a_lang_code);
			} else {
				self::get_instance()->language = $directory->get_translation(ApineAppConfig::get('languages', 'default'));
			}
		}
		
		$code = str_replace('-', '_', self::get_instance()->language->get_language()->code);
		setlocale(LC_ALL, $code . '.UTF8', $code, self::get_instance()->language->get_language()->code_short);
	
	}
	
	/**
	 * Detect the best language according to language cookie
	 *
	 * @return ApineTranslation
	 */
	private static function cookie_best () {
		
		if (ApineAppConfig::get('languages', 'use_cookie') === "yes" && ApineCookie::get('apine_language')) {
			$directory = new ApineTranslationDirectory();
			return $directory->is_exist_language(ApineCookie::get('apine_language'));
		} else {
			return null;
		}
	
	}
	
	/**
	 * Detect the best language according to language parameter in request
	 *
	 * @return ApineTranslation
	 */
	private static function request_best () {
		
		if (isset(ApineRequest::get()['language'])) {
			$directory = new ApineTranslationDirectory();
			return $directory->is_exist_language(ApineRequest::get()['language']);
		} else {
			return null;
		}
	
	}
	
	/**
	 * Detect the best language according to language headers
	 *
	 * @return ApineTranslation
	 */
	private static function user_agent_best () {
		
		$directory = new ApineTranslationDirectory();
		
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			return $directory->get_translation(ApineAppConfig::get('languages', 'default'))->get_language()->code;
		}
	
		$user_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$found_language = null;
	
		foreach ($user_languages as $lang) {
			$break = explode(';', $lang);
			$lang = $break[0];
				
			$best = $directory->is_exist_language($lang);
				
			if ($best) {
				$found_language = $best;
				break;
			}
		}
	
		if(isset($found_language)) {
			return $found_language;
		} else {
			return null;
		}
	
	}
	
	/**
	 * Fetch a translation string
	 *
	 * @param string $a_prefix
	 * @param string $a_key
	 * @param array $a_pattern
	 * @return string
	 */
	public static function translate ($a_prefix, $a_key = null, $a_pattern = null) {
	
		if (self::get_instance()->language == null) {
			self::set_language();
		}
	
		return self::get_instance()->language->parse($a_prefix, $a_key, $a_pattern);
	
	}
	
	/**
	 * Fetch current session language
	 *
	 * @return ApineTranslationLanguage
	 */
	public static function language () {
	
		if (self::get_instance()->language == null) {
			self::set_language();
		}
	
		return self::get_instance()->language->get_language();
	
	}
	
	/**
	 * Fetch current session language
	 *
	 * @return ApineTranslation
	 */
	public static function translation () {
	
		if (self::get_instance()->language == null) {
			self::set_language();
		}
	
		return self::get_instance()->language;
	
	}
	
}