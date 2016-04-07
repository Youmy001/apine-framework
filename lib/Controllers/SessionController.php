<?php
/**
 * Session Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use \Exception as Exception;
use Apine\Application as Application;
use Apine\MVC as MVC;
use Apine\Core as Core;
use Apine\Session as Session;
use Apine\User as User;
use Apine\Exception\GenericException as GenericException;
use Apine\Routing\WebRouter as WebRouter;

class SessionController extends MVC\Controller {
	
	public function login ($params) {
		
		if (Core\Request::is_post()) {
			
			if (isset($params['user'])) {
				$user = $params['user'];
			}
			
			if (isset($params['pwd'])) {
				$pwd = $params['pwd'];
			}
			
			if (isset($params['redirect'])) {
				$redirect = $params['redirect'];
			}
			
			if (isset($params['perm'])) {
				$permanent = true;
			} else {
				$permanent = false;
			}
			
			try {
				
				if ((isset($user) && isset($pwd)) && (!Session\SessionManager::is_logged_in())) {
					if (Session\SessionManager::login($user, $pwd, array("remember" => $permanent))) {
						if (isset($redirect) && $redirect != "") {
							header("Location: " . $redirect);
						} else {
							header("Location: " . MVC\URLHelper::path(""));
							die();
						}
						
						die();
					}
				}
				
				if (null == ($message = Application\ApplicationTranslator::translate('errors', 'login_invalid_username'))) {
					$message = 'Either the the username/email or the password is not valid. Please try again later.';
				}
			} catch (Exception $e) {
				if (null == ($message = Application\ApplicationTranslator::translate('errors', 'form_invalid'))) {
					$message = 'An unknown error occured when sending data to the server. Please try again later.';
				}
			}
			$this->_view->set_param('error_code', 500);
			$this->_view->set_param('error_message', $message);
		}
		
		if (null == ($title = Application\ApplicationTranslator::translate('login', 'title'))) {
			$title = 'Sign In';
		}
		
		$this->_view->set_title($title);
		$this->_view->set_view('session/login');
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
	public function logout () {
		
		if (Session\SessionManager::is_logged_in()) {
			Session\SessionManager::logout();
		}
		
		header("Location: " . MVC\URLHelper::path("login"));
		
	}
	
	public function register ($params) {
		
		if (Core\Request::is_post() && !Session\SessionManager::is_logged_in()) {
			
			if (isset($params['user'])) {
				$user = $params['user'];
			}
			
			if (isset($params['pwd'])) {
				$pwd = $params['pwd'];
			}
			
			if (isset($params['redirect'])) {
				$redirect = $params['redirect'];
			}
			
			if (isset($params['pwd_confirm'])) {
				$pwdconfirm = $params['pwd_confirm'];
			}
			
			if (isset($params['email'])) {
				$email = $params['email'];
			}
			
			try {
				
				// Make sure required data are provided
				if ((isset($user) && isset($pwd) && isset($email)) && (!Session\SessionManager::is_logged_in())) {
					
					// Verify the user name isn't already in use
					if (User\Factory\UserFactory::is_name_exist($user)) {
						throw new Exception("4"); // Already used username
					}
					
					// Verify both passwords are identical
					if (($pwd === $pwdconfirm)) {
						$encoded_pwd = Core\Encryption::hash_password($pwd);
					} else {
						throw new Exception("3"); // Wrong password
					}
					
					// Verify Email format if it exists
					if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
						throw new Exception("2"); // Misformated Email
					}
					
					if (User\Factory\UserFactory::is_email_exist($email)) {
						throw new Exception("7"); // Already used email
					}
					
					// Create and populate new empty user
					$class_name = Session\SessionManager::get_user_class();
					$new_user = new $class_name();
					$new_user->set_username($user);
					$new_user->set_password($encoded_pwd);
					$new_user->set_type(APINE_SESSION_USER);
					
					$list_group=new Core\Collection();
					$list_group->add_item(User\Factory\UserGroupFactory::create_by_id(1));
					$new_user->set_group($list_group);
					
					if (!empty($email)) {
						$new_user->set_email_address($email);
					}
					
					$new_user->save(); // Write new user in database
					
					if (isset($redirect) && $redirect != "") {
						header("Location: " . $redirect);
					} else {
						header("Location: " . MVC\URLHelper::path("login"));
						die();
					}
					
					die();
				}
				
				throw new Exception(0); // Unknown Error
			} catch (Exception $e) {
				
				switch ($e->getMessage()) {
					case 7:
						if (null == ($message = Application\ApplicationTranslator::translate('errors', 'register_taken_email'))) {
							$message = 'The email address is not valid.';
						}
						break;
					case 4:
						if (null == ($message = Application\ApplicationTranslator::translate('errors', 'register_taken_username'))) {
							$message= 'The username is already taken by an user.';
						}
						break;
					case 3:
						if (null == ($message = Application\ApplicationTranslator::translate('errors', 'register_invalid_password'))) {
							$message= 'The password does not match the confirmation.';
						}
						break;
					case 2:
						if (null == ($message = Application\ApplicationTranslator::translate('errors', 'register_invalid_email'))) {
							$message= 'The email address is not valid.';
						}
						break;
					case 0:
					default:
						if (null == ($message = Application\ApplicationTranslator::translate('errors', 'form_invalid'))) {
							$message= 'An unknown error occured when sending data to the server. Please try again later.';
						}
				}
				
			}
			
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		
		if (null == ($title = Application\ApplicationTranslator::translate('register', 'title'))) {
			$title = 'Sign Up';
		}
		
		$this->_view->set_title($title);
		$this->_view->set_view('session/register');
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
	public function restore ($params) {
		
		if (!Session\SessionManager::is_logged_in()){
			try {
				if (Core\Request::is_post()) {
					
					if (isset($params['action'])) {
						if ($params['action'] == 'code') {
							$action = "code";
						} else if ($params['action'] == 'reset') {
							$action = "restore";
						}
					} else {
						throw new GenericException('Bad Request', 400);
					}
				} else {
					if (count($params) == 1) {
						$action = "reset";
					} else {
						$action = "index";
					}
				}
				
				$router = new WebRouter();
				return $router->execute('restore', $action, $params);
			} catch (Exception $e) {
				throw new GenericException($e->getMessage(), $e->getCode(), $e);
			}
		} else {
			throw new GenericException('Forbidden', 403);
		}
		
	}
	
	public function api () {
	
		$this->_view->set_response_code(200);
		$this->_view->set_title('API Instructions');
		$this->_view->set_view('session/api');
		
		return $this->_view;
	
	}
}