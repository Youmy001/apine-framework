<?php
/**
 * Execution Translator
 * This script contains a facade for the Translator core module
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Application;

/*use Apine\Core\ApplicationConfig;
use Apine\Core\Cookie;
use Apine\Core\Request;*/
use Apine;

/**
 * Execution Translator
 * Manage Multi-Languages setups
 */
final class ApplicationTranslator {
	
	/**
	 * Instance of the Translation Manager
	 * Singleton Implementation
	 * 
	 * @var ApplicationTranslator
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
	 * @return ApplicationTranslator
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
		
		$directory = new Apine\Translation\TranslationDirectory();
		//$request = Apine\Core\Request::get_instance();
		$request_get = Apine\Core\Request::get();
		//$config = new ApineConfig('config.ini');
		
		if (is_null($a_lang_code)) {
			if (ApplicationConfig::get('localization', 'locale_detection') == "yes") {
				if (!isset($request_get['language'])) {
					$language = self::cookie_best();
					
					if (!$language) {
						$language = self::user_agent_best();
					}
				} else {
					$language = self::request_best();
				}
				
				if (!$language) {
					$language = ApplicationConfig::get('localization', 'locale_default');
				}
	
				self::get_instance()->language = $directory->get_translation($language);
			} else {
				if (isset($request_get['language'])) {
					$language = self::request_best();
				} else {
					$language = ApplicationConfig::get('localization', 'locale_default');
				}
				
				self::get_instance()->language = $directory->get_translation($language);
			}
		} else {
			if ($directory->is_exist_language($a_lang_code)) {
				self::get_instance()->language = $directory->get_translation($a_lang_code);
			} else {
				self::get_instance()->language = $directory->get_translation(ApplicationConfig::get('localization', 'locale_default'));
			}
		}
		
		$code = str_replace('-', '_', self::get_instance()->language->get_language()->code);
		setlocale(LC_ALL, $code . '.UTF8', $code, self::get_instance()->language->get_language()->code_short);
	
	}
	
	/**
	 * Detect the best language according to language cookie
	 *
	 * @return Translation
	 */
	private static function cookie_best () {
		
		$cookie = Apine\Core\Cookie::get('apine_language');
		
		if (ApplicationConfig::get('localization', 'locale_use_cookie') === "yes" && $cookie) {
			$directory = new Apine\Translation\TranslationDirectory();
			return $directory->is_exist_language($cookie);
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
		
		$request_get = Apine\Core\Request::get();
		
		if (isset($request_get['language'])) {
			$directory = new Apine\Translation\TranslationDirectory();
			return $directory->is_exist_language($request_get['language']);
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
		
		$directory = new Apine\Translation\TranslationDirectory();
		
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			return $directory->get_translation(ApplicationConfig::get('localization', 'locale_default'))->get_language()->code;
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