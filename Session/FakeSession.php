<?php
/**
 * This file contains the session management class for no-db apps
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Session;

use Apine;
use Apine\Application\Application;
use Apine\Autoloader;
use Apine\Core\Encryption;
use Apine\User\Factory\UserFactory;
use Apine\User\User;

/**
 * Gestion and configuration of the a user session on a web app
 * This class manages user login and logout
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
final class FakeSession implements SessionInterface {


	/**
	 * Construct the session handler
	 * Fetch data from PHP structures and start the PHP session
	 */
	public function __construct () {

	}
	
	/**
	 * Get PHP's session Id
	 * 
	 * @return string
	 */
	public function get_session_identifier () {
	
		return "";
	
	}

	/**
	 * Verifies if a user is logged in
	 * 
	 * @return boolean
	 */
	public function is_logged_in () {

		return false;
	
	}

	/**
	 * Get logged in user
	 * 
	 * @return Apine\User\User
	 */
	public function get_user () {

		return null;
	
	}

	/**
	 * Get logged in user's id
	 * 
	 * @return integer
	 */
	public function get_user_id () {

		return null;
	
	}

	/**
	 * Get current session access level
	 * 
	 * @return integer
	 */
	public function get_session_type () {

		return APINE_SESSION_GUEST;
	
	}

	/**
	 * Set current session access level
	 * 
	 * @param integer $a_type
	 *        Session access level type
     * @return integer
	 */
	public function set_session_type ($a_type) {

		return APINE_SESSION_GUEST;
	
	}
	
	public function is_session_admin () {
	
		return false;
	
	}
	
	public function is_session_normal () {
	
		return false;
	
	}
	
	public function is_session_guest () {
		
		return true;
		
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

        return false;
	
	}

	/**
	 * Log a user out
     *
     * @return boolean
     * @throws Apine\Exception\GenericException
	 */
	public function logout () {

		return false;
	
	}

}