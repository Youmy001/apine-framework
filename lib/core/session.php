<?php
/**
 * This file contains the session management class
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

/**
 * Gestion and configuration of the a user session
 * This class manages user login and logout and register the state of the http request
 */
class ApineSession {
	
	/**
	 * Instance of the Session Manager
	 * Singleton Implementation
	 * @var ApineSession
	 */
	private static $_instance;
	
	/**
	 * PHP session's Id
	 * @var string
	 */
	public $php_session_id;

	/**
	 * Is a user logged in or not
	 * @var boolean
	 */
	private $logged_in;

	/**
	 * Logged in user id
	 * @var integer
	 */
	private $user_id;

	/**
	 * Session timeout
	 * @var integer
	 */
	private $session_timeout = 1000;

	/**
	 * Session duration
	 * @var integer
	 */
	private $session_lifespan = 7200;

	/**
	 * Type of the current user
	 * @var integer
	 */
	private $session_type = SESSION_GUEST;
	
	/**
	 * Construct the session handler
	 * Fetch data from PHP structures and start the PHP session
	 */
	private function __construct () {
		
		// Check the session cookie (if one)
		if (Cookie::get('session') != null) {
			session_id(Cookie::get('session'));
		}
		
		// Start PHP Session
		session_start();
		
		// Set PHP session id
		$this->php_session_id = session_id();
		
		// Check if a user ID is in the PHP session
		if (isset($_SESSION['ID'])) {
			$this->logged_in = true;
			$this->user_id = $_SESSION['ID'];
			$this->session_type = ApineUserFactory::create_by_id($this->user_id)->get_type();
		} else {
			$this->logged_in = false;
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

	/**
	 * Verifies if a user is logged in
	 * @return boolean
	 */
	public static function is_logged_in () {

		return self::get_instance()->logged_in;
	
	}

	/**
	 * Get logged in user's id
	 * @return integer
	 */
	public static function get_user_id () {

		if (self::is_logged_in()) {
			return $this->user_id;
		}
	
	}

	/**
	 * Get logged in user
	 * @return ApineUser
	 */
	public static function get_user () {

		if (self::is_logged_in()) {
			return new ApineUser(self::get_instance()->user_id);
		}
	
	}

	/**
	 * Get PHP's session Id
	 * @return string
	 */
	public static function get_session_identifier () {

		return self::get_instance()->php_session_id;
	
	}

	/**
	 * Set current session access level
	 * @param integer $type
	 *        Session access level type
	 */
	public static function set_session_type ($type) {

		self::get_instance()->session_type = $type;
	
	}

	/**
	 * Get current session access level
	 * @return integer
	 */
	public static function get_session_type () {

		return self::get_instance()->session_type;
	
	}

	/**
	 * Log a user in
	 * Look up in database for a matching row with a username and a password
	 * 
	 * @param string $user_name
	 *        Username of the user
	 * @param string $password
	 *        Password of the user
	 * @return boolean
	 */
	public static function login ($user_name, $password) {

		if (!self::is_logged_in()) {
			if ((ApineUserFactory::is_name_exist($user_name) || ApineUserFactory::is_email_exist($user_name)) && 
								ApineUserFactory::create_by_name($user_name)->get_register_date()<"2015-09-04") {
				$encode_pass = hash('sha256', $password);
			} else {
				$encode_pass = Encryption::hash_password($password, ApineUserFactory::create_by_name($user_name)->get_username());
			}
			
			$user_id = ApineUserFactory::authentication($user_name, $encode_pass);
			
			if ($user_id) {
				self::get_instance()->user_id = $user_id;
				self::get_instance()->logged_in = true;
				$_SESSION['ID'] = $user_id;
				$new_user = self::get_user();
				$_SESSION['type'] = $new_user->get_type();
				self::set_session_type($new_user->get_type());
				return true;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
	
	}

	/**
	 * Log a user out
	 */
	public static function logout () {

		if (self::is_logged_in()) {
			$_SESSION = array();
			Cookie::set('session', self::get_instance()->php_session_id, time() - 604800);
			session_destroy();
			self::get_instance()->logged_in = false;
			self::set_session_type(SESSION_GUEST);
		}
	
	}

	/**
	 * Make a logged un user's session permanent
	 */
	public static function make_permanent () {

		if (self::is_logged_in()) {
			Cookie::set('session', self::get_instance()->php_session_id, time() + 604800);
		}
	
	}

}