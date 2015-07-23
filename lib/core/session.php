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
class ApineSession{

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
	 * Database connection handler
	 * @var Database
	 */
	static $dbhandle;

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
	 * Session Language
	 * @var Language
	 */
	private $session_language;

	/**
	 * Type of the current user
	 * @var integer
	 */
	private $session_type = SESSION_GUEST;
	
	/**
	 * Session Request Type
	 * @var string
	 */
	private $session_request_type;
	
	/**
	 * Construct the session handler
	 * Fetch data from PHP structures and start the PHP session
	 */
	public function __construct(){
		
		$this->session_request_type=$_SERVER['REQUEST_METHOD'];
		
		// Check the session cookie (if one)
		if(Cookie::get_cookie('session') != null){
			session_id(Cookie::get_cookie('session'));
		}
		// Start PHP Session
		session_start();
		// Set PHP session id
		$this->php_session_id = session_id();
		// Check if a user ID is in the PHP session
		if(isset($_SESSION['ID'])){
			$this->logged_in = true;
			$this->user_id = $_SESSION['ID'];
			$this->session_type = $_SESSION['type'];
		}
	
	}
	
	/**
	 * Return the type of the current http request
	 * @return string
	 */
	public function get_session_request_type(){
		return $this->session_request_type;
	}
	
	/**
	 * Returns weither the current http request is a GET request or not
	 * @return boolean
	 */
	public function is_get(){
		$return=false;
		if($this->session_request_type=="GET"){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a POST request or not
	 * @return boolean
	 */
	public function is_post(){
		$return=false;
		if($this->session_request_type=="POST"){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a PUT request or not
	 * @return boolean
	 */
	public function is_put(){
		$return=false;
		if($this->session_request_type=="PUT"){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a DELETE request or not
	 * @return boolean
	 */
	public function is_delete(){
		$return=false;
		if($this->session_request_type=="DELETE"){
			$return=true;
		}
		return $return;
	}

	/**
	 * Verifies if a user is logged in
	 * @return boolean
	 */
	public function is_logged_in(){

		return $this->logged_in;
	
	}

	/**
	 * Get logged in user's id
	 * @return integer
	 */
	public function get_user_id(){

		if($this->is_logged_in())
			return $this->user_id;
	
	}

	/**
	 * Get logged in user
	 * @return User
	 */
	public function get_user(){

		if($this->is_logged_in())
			return new ApineUser($this->user_id);
	
	}

	/**
	 * Get PHP's session Id
	 * @return string
	 */
	public function get_session_identifier(){

		return $this->php_session_id;
	
	}

	/**
	 * Set current session access level
	 * @param integer $type
	 *        Session access level type
	 */
	public function set_session_type($type){

		$this->session_type = $type;
	
	}

	/**
	 * Get current session access level
	 * @return integer
	 */
	public function get_session_type(){

		return $this->session_type;
	
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
	public function Login($user_name, $password){

		if(!$this->is_logged_in()){
			$encode_pass = hash('sha256', $password);
			
			$user_id = ApineUserFactory::authentication($user_name, $encode_pass);
			if($user_id){
				$this->user_id = $user_id;
				$this->logged_in = true;
				$_SESSION['ID'] = $this->user_id;
				$new_user = $this->get_user();
				$_SESSION['type'] = $new_user->get_type();
				$this->set_session_type($new_user->get_type());
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	
	}

	/**
	 * Log a user out
	 */
	public function Logout(){

		if($this->is_logged_in()){
			$_SESSION = array();
			Cookie::set_cookie('session', $this->php_session_id, time() - 7776000);
			session_destroy();
			$this->logged_in = false;
			$this->set_session_type(SESSION_GUEST);
		}
	
	}

	/**
	 * Make a logged un user's session permanent
	 */
	public function make_permanent(){

		if($this->is_logged_in())
			self::set_cookie('session', $this->php_session_id, time() + 7776000);
	
	}

}