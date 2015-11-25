<?php
/**
 * This file contains the session management class for web apps
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Gestion and configuration of the a user session on a web app
 * This class manages user login and logout
 */
final class ApineWebSession implements ApineSessionInterface {

	/**
	 * PHP session's Id
	 * 
	 * @var string
	 */
	private $php_session_id;

	/**
	 * Is a user logged in or not
	 * 
	 * @var boolean
	 */
	private $logged_in;

	/**
	 * Logged in user id
	 * 
	 * @var integer
	 */
	private $user_id;

	/**
	 * Name of the user class
	 * 
	 * @var string
	 */
	private $user_class_name;

	/**
	 * Session timeout
	 * 
	 * @var integer
	 */
	private $session_timeout = 1000;

	/**
	 * Session duration
	 * 
	 * @var integer
	 */
	private $session_lifespan = 7200;

	/**
	 * Type of the current user
	 * 
	 * @var integer
	 */
	private $session_type = APINE_SESSION_GUEST;

	/**
	 * Construct the session handler
	 * Fetch data from PHP structures and start the PHP session
	 */
	public function __construct () {
		
		// Check the session cookie (if one)
		if (ApineCookie::get('session') != null) {
			session_id(ApineCookie::get('session'));
		}
		
		// Start PHP Session
		session_start();
		
		// Set PHP session id
		$this->php_session_id = session_id();
		
		if (ApineConfig::get('runtime', 'user_class')) {
			$pos_slash = strpos(ApineConfig::get('runtime', 'user_class'), '/');
			$module = substr(ApineConfig::get('runtime', 'user_class'), 0, $pos_slash);
			$class = substr(ApineConfig::get('runtime', 'user_class'), $pos_slash + 1);
			load_module($module);
			
			if (class_exists($class) && is_subclass_of($class, 'ApineUser')) {
				$this->user_class_name = $class;
			} else {
				$this->user_class_name = 'ApineUser';
			}
		
		} else {
			$this->user_class_name = "ApineUser";
		}
		
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
	 * Get PHP's session Id
	 * 
	 * @return string
	 */
	public function get_session_identifier () {
	
		return $this->php_session_id;
	
	}

	/**
	 * Verifies if a user is logged in
	 * 
	 * @return boolean
	 */
	public function is_logged_in () {

		return (boolean) $this->logged_in;
	
	}

	/**
	 * Get logged in user
	 * 
	 * @return ApineUser
	 */
	public function get_user () {

		if ($this->is_logged_in()) {
			$class = $this->user_class_name;
			
			$instance = new $class($this->user_id);
			
			return $instance;
		}
	
	}

	/**
	 * Get logged in user's id
	 * 
	 * @return integer
	 */
	public function get_user_id () {

		if ($this->is_logged_in()) {
			return $this->user_id;
		}
	
	}

	/**
	 * Get the name of the user class in use
	 *
	 * @return string
	 */
	public function get_user_class () {

		return $this->user_class_name;
	
	}

	/**
	 * Get current session access level
	 * 
	 * @return integer
	 */
	public function get_session_type () {

		return $this->session_type;
	
	}

	/**
	 * Set current session access level
	 * 
	 * @param integer $a_type
	 *        Session access level type
	 */
	public function set_session_type ($a_type) {

		$constants = get_defined_constants(true);
		$constants = $constants['user'];
		$type = false;
		
		foreach ($constants as $name => $value) {
			if(strstr($name, 'APINE_SESSION') && $value == $a_type) {
				$type = $a_type;
				$this->session_type = $a_type;
			}
		}
		
		return $type;
	
	}

	/**
	 * Log a user in
	 * Look up in database for a matching row with a username and a
	 * password
	 *
	 * @param string $user_name
	 *        Username of the user
	 * @param string $password
	 *        Password of the user
	 * @param string[] $options
	 *        Login Options
	 * @return boolean
	 */
	public function login ($user_name, $password, $options = array()) {

		if (!$this->is_logged_in()) {
			if ((ApineUserFactory::is_name_exist($user_name) || ApineUserFactory::is_email_exist($user_name))) {
				$encode_pass = ApineEncryption::hash_password($password);
			} else {
				return false;
			}
			
			$user_id = ApineUserFactory::authentication($user_name, $encode_pass);
			
			if ($user_id) {
				$this->user_id = $user_id;
				$this->logged_in = true;
				$_SESSION['ID'] = $user_id;
				$new_user = $this->get_user();
				$_SESSION['type'] = $new_user->get_type();
				$this->set_session_type($new_user->get_type());
				
				if (isset($options["remember"]) && $options["remember"] === true) {
					$this->make_permanent();
				}
				
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
	public function logout () {

		try {
			if ($this->is_logged_in()) {
				$_SESSION = array();
				Cookie::set('session', $this->php_session_id, time() - 604801);
				session_destroy();
				$this->logged_in = false;
				$this->set_session_type(SESSION_GUEST);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			throw new ApineException($e->getMessage(), $e->getCode(), $e);
			return false;
		}
	
	}

	/**
	 * Make a logged un user's session permanent
	 */
	private function make_permanent () {

		if ($this->is_logged_in()) {
			ApineCookie::set('session', $this->php_session_id, time() + 604800);
		}
	
	}

}