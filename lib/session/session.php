<?php
/**
 * This file contains the session management strategy
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * #@+
 * Constants
 */
define('SESSION_ADMIN', 77);
// Website administrators.
// Can access to everything. Can try new features before any one.
define('SESSION_USER', 65);
// Regular users. Can publish posts, videos, drawings and sound,
// comment blog articles and other posts.
// Can't access ACP.
// Can access to a limited set of options in UCP.
define('SESSION_GUEST', 40);
// Can't access ACP or UCP at all. Can only view content.
// Can also subscribe or register to newsletter.

class ApineSession {

	/**
	 * Instance of the implementation
	 * 
	 * @var ApineWebSession|ApineAPISession
	 */
	private $strategy;

	/**
	 * Instance of the Session Manager
	 * Singleton Implementation
	 * 
	 * @var ApineSession
	 */
	private static $_instance;

	private function __construct () {
		
		if (Request::is_api_call()) {
			$this->strategy = new ApineAPISession();
		} else {
			$this->strategy = new ApineWebSession();
		}
		
	}
	
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
	
	public static function get_session_identifier () {
		
		return self::get_instance()->strategy->get_session_identifier();
		
	}

	public static function is_logged_in () {
		
		return self::get_instance()->strategy->is_logged_in();

	}

	public static function get_user () {
		
		return self::get_instance()->strategy->get_user();

	}

	public static function get_user_id () {
		
		return self::get_instance()->strategy->get_user_id();

	}

	public static function get_session_type () {
		
		return self::get_instance()->strategy->get_session_type();

	}

	public static function set_session_type ($a_type) {
		
		return self::get_instance()->strategy->set_session_type($a_type);

	}

	public static function login ($username, $password) {
		
		return self::get_instance()->strategy->login($username, $password);

	}

	public static function logout () {
		
		return self::get_instance()->strategy->logout();

	}

}

interface ApineSessionInterface {

	public function is_logged_in ();

	public function get_user ();

	public function get_user_id ();
	
	public function get_session_type ();
	
	public function set_session_type ($a_type);
	
	public function login ($username, $password);
	
	public function logout ();

}