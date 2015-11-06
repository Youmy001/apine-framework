<?php
load_module('email');

class SessionController extends Controller {
	
	public function login ($params) {
		
		if (ApineRequest::is_post()) {
			
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
				
				if ((isset($user) && isset($pwd)) && (!ApineSession::is_logged_in())) {
					if (ApineSession::login($user, $pwd, array("remember" => $permanent))) {
						if (isset($redirect) && $redirect != "") {
							header("Location: " . $redirect);
						} else {
							header("Location: " . URL_Helper::path(""));
							die();
						}
						
						die();
					}
				}
				
				//$message = 'Either the the username/email or the password is not valid. Please try again later.';
				$message = ApineAppTranslator::translate('errors', 'login_invalid_username');
			} catch (Exception $e) {
				//var_dump($e);
				//$message = 'An unknown error occured when sending data to the server. Please try again later.';
				$message = ApineAppTranslator::translate('errors', 'form_invalid');
			}
			
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		
		$this->_view->set_title(ApineAppTranslator::translate('login', 'title'));
		//$this->_view->set_title('Login');
		$this->_view->set_view('session/login');
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
	public function logout () {
		
		if (ApineSession::is_logged_in()) {
			ApineSession::logout();
		}
		
		header("Location: " . URL_Helper::path("login"));
		
	}
	
	public function register ($params) {
		
		if (ApineRequest::is_post()&&!ApineSession::is_logged_in()) {
			
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
				if ((isset($user) && isset($pwd) && isset($email)) && (!ApineSession::is_logged_in())) {
					
					// Verify the user name isn't already in use
					if (ApineUserFactory::is_name_exist($user)) {
						throw new Exception("4"); // Already used username
					}
					
					// Verify both passwords are identical
					if (($pwd === $pwdconfirm)) {
						$encoded_pwd = ApineEncryption::hash_password($pwd);
					} else {
						throw new Exception("3"); // Wrong password
					}
					
					// Verify Email format if it exists
					if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
						throw new Exception("2"); // Misformated Email
					}
					
					if (ApineUserFactory::is_email_exist($email)) {
						throw new Exception("7"); // Already used email
					}
					
					// Create and populate new empty user
					$new_user = new ApineUser();
					$new_user->set_username($user);
					$new_user->set_password($encoded_pwd);
					$new_user->set_type(APINE_SESSION_USER);
					
					$list_group=new Liste();
					$list_group->add_item(ApineUserGroupFactory::create_by_id(1));
					$new_user->set_group($list_group);
					
					if (!empty($email)) {
						$new_user->set_email_address($email);
					}
					
					$new_user->save(); // Write new user in database
					
					if (isset($redirect) && $redirect != "") {
						header("Location: " . $redirect);
					} else {
						header("Location: " . URL_Helper::path("login"));
						die();
					}
					
					die();
				}
				
				throw new Exception(0); // Unknown Error
			} catch (Exception $e) {
				
				switch ($e->getMessage()) {
					case 7:
						$message = ApineAppTranslator::translate('errors', 'register_taken_email');
						break;
					case 4:
						$message = ApineAppTranslator::translate('errors', 'register_taken_username');
						break;
					case 3:
						$message = ApineAppTranslator::translate('errors', 'register_invalid_password');
						break;
					case 2:
						$message = ApineAppTranslator::translate('errors', 'register_invalid_email');
						break;
					case 0:
					default:
						$message = ApineAppTranslator::translate('errors', 'form_invalid');
				}
				
			}
			
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		
		$this->_view->set_title(ApineAppTranslator::translate('register', 'title'));
		//$this->_view->set_title("Sign In");
		$this->_view->set_view('session/register');
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
	public function restore ($params) {
		
		if (!ApineSession::is_logged_in()){
			try {
				if (ApineRequest::is_post()) {
					
					if (isset($params['action'])) {
						if ($params['action'] == 'code') {
							$action = "code";
						} else if ($params['action'] == 'reset') {
							$action = "restore";
						}
					} else {
						throw new ApineException('Bad Request', 400);
					}
				} else {
					if (count($params) == 1) {
						$action = "reset";
					} else {
						$action = "index";
					}
				}
			
				return ApineRouter::execute('restore', $action, $params);
			} catch (Exception $e) {
				throw new ApineException($e->getMessage(), $e->getCode(), $e);
			}
		} else {
			throw new ApineException('Forbidden', 403);
		}
		
	}
	
	public function api () {
	
		$this->_view->set_response_code(200);
		$this->_view->set_title('API Instructions');
		$this->_view->set_view('session/api');
		
		$this->_view;
	
	}
}