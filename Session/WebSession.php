<?php
/**
 * This file contains the session management class for web apps
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Session;

use Apine;

/**
 * Gestion and configuration of the a user session on a web app
 * This class manages user login and logout
 */
final class WebSession implements SessionInterface {

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
	 * Instance of logged in user
	 * 
	 * @var ApineUser
	 */
	private $user;

	/**
	 * Session duration
	 * 
	 * @var integer
	 */
	private $session_lifespan = 7200;
	
	/**
	 * Session duration with permanent option
	 *
	 * @var integer
	 */
	private $session_permanent_lifespan = 604800;

	/**
	 * Type of the current user
	 * 
	 * @var integer
	 */
	private $session_type = APINE_SESSION_GUEST;
	
	/**
	 * Session Entity
	 * 
	 * @var ApineSessionData
	 */
	private $session;

	/**
	 * Construct the session handler
	 * Fetch data from PHP structures and start the PHP session
	 */
	public function __construct () {
		
		//$config = new ApineConfig();
		$config = Apine\Application\Application::get_instance()->get_config();
		
		if ($config->get('runtime', 'user_class')) {
			$user_class = $config->get('runtime', 'user_class');
			$pos_slash = strpos($user_class, '/');
			$module = substr($user_class, 0, $pos_slash);
			$class = substr($user_class, $pos_slash + 1);
			apine_load_module($module);
			
			if (is_a($class, 'Apine\User\User')) {
				$this->user_class_name = $class;
			} else {
				$this->user_class_name = 'Apine\User\User';
			}
		
		} else {
			$this->user_class_name = "Apine\User\User";
		}
		
		if (!is_null($config->get('runtime', 'session_lifespan'))) {
			$this->session_lifespan = $config->get('runtime', 'session_lifespan');
		}
		
		if (isset($_COOKIE['apine_session'])) {
			$token = $_COOKIE['apine_session'];
		} else {
			$token = Apine\Core\Encryption::token();
		}
		
		$this->session = new SessionData($token);
		$this->php_session_id = $token;
		$delay = $this->session_lifespan;
		
		if ($this->session->get_var('id') != null) {
			$this->logged_in = false;
			
			if ($this->session->get_var('permanent') != null) {
				if ($this->session->is_valid($this->session_permanent_lifespan)) {
					$this->logged_in = true;
					$this->user_id  = $this->session->get_var('id');
					$this->session_type = $this->session->get_var('type');
					$delay = $this->session_permanent_lifespan;
				} else {
					$this->session->reset();
				}
			} else {
				if ($this->session->is_valid($this->session_lifespan)) {
					$this->logged_in = true;
					$this->user_id  = $this->session->get_var('id');
					$this->session_type = $this->session->get_var('type');
				} else {
					$this->session->reset();
				}
			}
		} else {
			$this->logged_in = false;
		}
		
		setcookie('apine_session', $this->php_session_id, time() + $delay, '/');
	
	}
	
	/**
	 * Get PHP's session Id
	 * 
	 * @return string
	 */
	public function get_session_identifier () {
	
		return $this->php_session_id;
	
	}
	
	public function &data () {
		
		return $this->session;
		
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
	 * @return Apine\User\User
	 */
	public function get_user () {

		if ($this->is_logged_in()) {
			if (is_null($this->user)) {
				$this->user = Apine\User\Factory\UserFactory::create_by_id($this->user_id);
			}
			
			return $this->user;
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
	
	public function is_session_admin () {
	
		return ($this->session_type == APINE_SESSION_ADMIN) ? true : false;
	
	}
	
	public function is_session_normal () {
	
		return ($this->session_type == APINE_SESSION_USER) ? true : false;
	
	}
	
	public function is_session_guest () {
		
		return ($this->session_type == APINE_SESSION_GUEST) ? true : false;
		
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
			if ((Apine\User\Factory\UserFactory::is_name_exist($user_name) || Apine\User\Factory\UserFactory::is_email_exist($user_name))) {
				$encode_pass = Apine\Core\Encryption::hash_password($password);
			} else {
				return false;
			}
			
			$user_id = Apine\User\Factory\UserFactory::authentication($user_name, $encode_pass);
			
			if ($user_id) {
				$session_data = array();
				$this->user_id = $user_id;
				$this->logged_in = true;
				$new_user = $this->get_user();
				$this->set_session_type($new_user->get_type());
				
				//$session_data['id'] = $user_id;
				//$session_data['type'] = $new_user->get_type();
				$this->session->set_var('id', $user_id);
				$this->session->set_var('type', $new_user->get_type());
				
				if (isset($options["remember"]) && $options["remember"] === true) {
					//$session_data['permanent'] = true;
					$this->session->set_var('permanent', true);
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
				//$_SESSION = array();
				//ApineCookie::set('apine_session', $this->php_session_id, time() - 604801);
				$this->session->reset();
				$this->logged_in = false;
				$this->set_session_type(APINE_SESSION_GUEST);
				return true;
			} else {
				return false;
			}
		} catch (\Exception $e) {
			throw new Apine\Exception\GenericException($e->getMessage(), $e->getCode(), $e);
			return false;
		}
	
	}
	
	/*public function __destruct() {
		
		$this->session->save();
		
	}*/

}