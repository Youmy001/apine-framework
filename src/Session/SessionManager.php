<?php
/**
 * This file contains the session management strategy
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Session;

use Apine;
use Apine\User\User;

/**
 * Abstraction for session management using the strategy pattern
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
final class SessionManager {

	/**
	 * Instance of the implementation
	 * 
	 * @var SessionInterface
	 */
	private $strategy;

	/**
	 * Instance of the Session Manager
	 * Singleton Implementation
	 * 
	 * @var SessionManager
	 */
	private static $_instance;

	/**
	 * Instantiation of the strategy
	 */
	private function __construct () {
		
		if (Apine\Core\Request::is_api_call()) {
			$request = Apine\Core\Request::get_instance();
			
			if (isset($request->get_request_headers()['Authorization'])) {
				$this->strategy = new APISession();
			} else if (isset($_COOKIE['apine_session'])) {
				$this->strategy = new WebSession();
			} else {
				$this->strategy = new APISession();
			}
		} else {
			$this->strategy = new WebSession();
		}
		
	}
	
	/**
	 * Singleton design pattern implementation
	 * 
	 * @return SessionManager
	 */
	public static function get_instance () {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	/**
	 * Fetch the unique identifier for the current session
	 * 
	 * @return string
	 */
	public static function get_session_identifier () {
		
		return self::get_instance()->strategy->get_session_identifier();
		
	}

	/**
	 * Verifies if a user is logged in
	 * 
	 * @return boolean
	 */
	public static function is_logged_in () {
		
		return self::get_instance()->strategy->is_logged_in();

	}

	/**
	 * Get logged in user
	 * 
	 * @return Apine\User\User
	 */
	public static function get_user () {
		
		return self::get_instance()->strategy->get_user();

	}

	/**
	 * Get logged in user's id
	 * 
	 * @return integer
	 */
	public static function get_user_id () {
		
		return self::get_instance()->strategy->get_user_id();

	}

	/**
	 * Get current session access level
	 * 
	 * @return integer
	 */
	public static function get_session_type () {
		
		return self::get_instance()->strategy->get_session_type();

	}

	/**
	 * Set current session access level
	 *
	 * @param integer $a_type
	 *        Session access level type
     * @return integer
	 */
	public static function set_session_type ($a_type) {
		
		return self::get_instance()->strategy->set_session_type($a_type);

	}
	
	/**
	 * Return current session lifespan
	 *
	 * @return integer
	 */
	public function get_session_lifespan () {
	
		return self::get_instance()->strategy->get_session_lifespan();
	}
	
	/**
	 * Verify if the current session access level is admin
	 * 
	 * @return boolean
	 */
	public static function is_session_admin () {
	
		return self::get_instance()->strategy->is_session_admin();
	
	}
	
	/**
	 * Verify if the current session access level is normal user
	 *
	 * @return boolean
	 */
	public static function is_session_normal () {
	
		return self::get_instance()->strategy->is_session_normal();
	
	}
	
	/**
	 * Verify if the current session access level is guest
	 *
	 * @return boolean
	 */
	public static function is_session_guest () {
		
		return self::get_instance()->strategy->is_session_guest();
		
	}

	/**
	 * Log a user in
	 *
	 * @param string $username
	 *        Username of the user
	 * @param string $password
	 *        Password of the user
	 * @return boolean
	 */
	public static function login ($username, $password) {
		
		if(func_num_args() === 3) {
			$options = func_get_arg(2);
		} else {
			$options = array();
		}
		
		return self::get_instance()->strategy->login($username, $password, $options);

	}

	/**
	 * Log a user out
	 * 
	 * @return boolean
	 */
	public static function logout () {
		
		return self::get_instance()->strategy->logout();

	}
	
	/**
	 * Returns the session handler
	 * 
	 * @return SessionInterface
	 */
	public static function get_handler () {
		
		return self::get_instance()->strategy;
		
	}

}