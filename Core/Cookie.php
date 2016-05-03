<?php
/**
 * Cookie Access tool
 * This script contains an helper to read and write cookies
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

/**
 * Cookie writing and reading tool
 * 
 * Tool to easily read and write cookies
 */
final class Cookie {
	
	/**
	 * Get cookie by name
	 * 
	 * @param string $cookie_name
	 * @return string
	 */
	public static function get ($cookie_name) {
	
		if (isset($_COOKIE[$cookie_name]))
			return $_COOKIE[$cookie_name];
	
	}
	
	/**
	 * Set a new cookie value
	 * 
	 * @param string $cookie_name
	 * @param string $value
	 * @param integer $expiration_time
	 *        Expiration date in miliseconds
	 * @return boolean
	 */
	public static function set ($cookie_name, $value, $expiration_time = 0) {
	
		$ar_domain = explode('.', $_SERVER['SERVER_NAME']);
		
		if (count($ar_domain) >= 3) {
			$start = strlen($ar_domain[0]) + 1;
			$main_session_server = substr($_SERVER['SERVER_NAME'], $start);
		} else {
			$main_session_server = $_SERVER['SERVER_NAME'];
		}
		
		if ($expiration_time == 0) {
			return setcookie($cookie_name, $value, null, '/');
		}else {
			return setcookie($cookie_name, $value, $expiration_time, '/');
		}
	
	}
}
