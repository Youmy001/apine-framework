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
final class ApineTranslator {
	
	/**
	 * Instance of the Session Manager
	 * Singleton Implementation
	 * 
	 * @var ApineSession
	 */
	private static $_instance;
	
	/**
	 * Current Language
	 * 
	 * @var Translation
	 */
	private $language;
	
	/**
	 * Singleton design pattern implementation
	 * @return ApineSession
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
		
		if (is_null($a_lang_code)) {
			if (Config::get('languages', 'detection') == "yes") {
				$language = self::user_agent_best();
				$language = self::cookie_best();
				$language = self::request_best();
	
				if (!$language) {
					$language = Translator::get_translation(Config::get('languages', 'default'));
				}
	
				self::get_instance()->language = $language;
			} else {
				self::get_instance()->language = Translator::get_translation(Config::get('languages', 'default'));
			}
		} else {
			if (Translator::is_exist_language($a_lang_code)) {
				self::get_instance()->language = Translator::get_translation($a_lang_code);
			} else {
				self::get_instance()->language = Translator::get_translation(Config::get('languages', 'default'));
			}
		}
		
		setlocale(LC_TIME, self::get_instance()->language->get_language()->code);
		
		/*if (ApineSession::is_logged_in()) {
			// If User is logged in and has a custom language
		}*/
	
	}
	
	/**
	 * Detect the best language according to language cookie
	 *
	 * @return Translation
	 */
	private static function cookie_best () {
	
		if (Config::get('languages', 'use_cookie') === "yes" && Cookie::get('language')) {
			return self::best(Cookie::get('language'));
		} else {
			return null;
		}
	
	}
	
	/**
	 * Detect the best language according to language parameter in request
	 *
	 * @return Translation
	 */
	private static function request_best () {
	
		if (isset(Request::get()['language'])) {
			return self::best(Request::get()['language']);
		} else {
			return null;
		}
	
	}
	
	/**
	 * Detect the best language according to language headers
	 *
	 * @return Translation
	 */
	private static function user_agent_best () {
	
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			return Translator::get_translation(Config::get('languages', 'default'))->get_language()->code;
		}
	
		$user_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$found_language = null;
	
		foreach ($user_languages as $lang) {
			$break = explode(';', $lang);
			$lang = $break[0];
				
			$best = self::best($lang);
				
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
	 * Guess the best language to use from a ISO language identifier
	 *
	 * @param string $a_language_code
	 * @return Translation
	 */
	private static function best ($a_language_code) {
	
		if (Translator::is_exist_language($a_language_code)) {
			$is_found = true;
			$found_language = Translator::get_translation($a_language_code);
		} else {
			if (strlen($a_language_code) > 2) {
				$a_language_code = substr($a_language_code, 0, 2);
			}
				
			$matches = array();
			$translations = array();
	
			foreach (Translator::get_all_languages() as $item) {
				if($item->code_short == $a_language_code) {
					$translation = Translator::get_translation($item->code);
					$matches[$item->code] = $translation->get('language','priority');
					$translations[$item->code] = $translation;
				}
			}
	
			if (count($matches) == 1) {
				$is_found = true;
				$found_language = reset($translations);
			} else if (count($matches) > 1) {
				$is_found = true;
				arsort($matches);
				$keys = array_keys($matches);
				$found_language = $translations[reset($keys)];
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
	 * @return string
	 */
	public static function translate ($a_prefix, $a_key = null) {
	
		if (self::get_instance()->language == null) {
			self::set_language();
		}
	
		return self::get_instance()->language->get($a_prefix, $a_key);
	
	}
	
	/**
	 * Fetch current session language
	 *
	 * @return TranslationLanguage
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
	 * @return Translation
	 */
	public static function translation () {
	
		if (self::get_instance()->language == null) {
			self::set_language();
		}
	
		return self::get_instance()->language;
	
	}
	
}