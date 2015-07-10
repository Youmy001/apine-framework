<?php

class Cookie {
	/**
	 * Get cookie by name
	 * @param string $cookie_name
	 * @return string
	 */
	public static function get_cookie($cookie_name){
	
		if(isset($_COOKIE[$cookie_name]))
			return $_COOKIE[$cookie_name];
	
	}
	
	/**
	 * Set a new cookie value
	 * @param string $cookie_name
	 * @param string $value
	 * @param integer $expiration_time
	 *        Expiration date in miliseconds
	 * @return boolean
	 */
	public static function set_cookie($cookie_name, $value, $expiration_time = 0){
	
		if($expiration_time == 0){
			$expiration_time = time() + 72000;
		}
		$ar_domain = explode('.', $_SERVER['SERVER_NAME']);
		if(count($ar_domain) >= 3){
			$start = strlen($ar_domain[0]) + 1;
			$main_session_server = substr($_SERVER['SERVER_NAME'], $start);
		}else{
			$main_session_server = $_SERVER['SERVER_NAME'];
		}
		return setcookie($cookie_name, $value, $expiration_time, '/', $main_session_server);
	
	}
}