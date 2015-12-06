<?php
/**
 * Authentication Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

class AuthController extends ApineAPIController {
	
	public function get ($params) {
		
		if (count($params) == 2) {
			
			$auth_username = $params[0];
			$auth_password = base64_decode($params[1]);
			
			try {
				if (!ApineSession::is_logged_in()) {
					if (ApineSession::login($auth_username, $auth_password)) {
						$this->_view->set_param('username', $auth_username);
						$this->_view->set_param('token', ApineSession::get_handler()->get_token()->get_token());
						$this->_view->set_param('origin', ApineSession::get_handler()->get_token()->get_origin());
						$this->_view->set_param('expiration', ApineSession::get_handler()->get_expiration_time());
					} else {
						throw new ApineException('Login Failed', 401);
					}
				} else {
					throw new ApineException('Unauthorized', 401);
				}
			} catch (Exception $e) {
				throw new ApineException($e->getMessage(), $e->getCode(), $e);
			}
			
			$this->_view->set_response_code(200);
			return $this->_view;
		} else {
			throw new ApineException("Missing arguments", 400);
		}
		
	}
	
	public function post ($params) {
		
		if (count($params) == 4) {
			try {
				if (!ApineSession::is_logged_in()) {
					if (isset($params['username'])) {
						$user = $params['username'];
					}
						
					if (isset($params['password'])) {
						$pwd = $params['password'];
					}
						
					if (isset($params['password_confirm'])) {
						$pwdconfirm = $params['password_confirm'];
					}
						
					if (isset($params['email'])) {
						$email = $params['email'];
					}
					
					if ((isset($user) && isset($pwd) && isset($email))) {
						// Verify data are valid
						if (ApineUserFactory::is_name_exist($user) || !($pwd === $pwdconfirm) || !filter_var($email,FILTER_VALIDATE_EMAIL) || ApineUserFactory::is_email_exist($email)) {
							throw new ApineException("Invalid Information", 400); // Already used username
						}
							
						$encoded_pwd = ApineEncryption::hash_password(base64_decode($pwd));
						
						// Create and populate new empty user
						$new_user = new ApineUser();
						$new_user->set_username($user);
						$new_user->set_password($encoded_pwd);
						$new_user->set_type(APINE_SESSION_USER);
							
						$list_group=new ApineCollection();
						$list_group->add_item(ApineUserGroupFactory::create_by_id(1));
						$new_user->set_group($list_group);
							
						if (!empty($email)) {
							$new_user->set_email_address($email);
						}
							
						$new_user->save(); // Write new user in database
						
						$this->_view->set_header_rule('Location', '/api/user/' . $new_user->get_username());
						$this->_view->set_param('username', $new_user->get_username());
					} else {
						throw new ApineException('Missing Arguments', 400);
					}
				} else {
					throw new ApineException('Unauthorized', 401);
				}
			} catch (Exception $e) {
				throw new ApineException($e->getMessage(), $e->getCode(), $e);
			}
		} else {
			throw new ApineException("Missing arguments", 400);
		}
		
		//throw new ApineException('Not Implemented', 501);
		$this->_view->set_response_code(201);
		return $this->_view;
		
	}
	
	public function put ($params) {
		
		throw new ApineException("Method Not Allowed", 405);
		
	}
	
	public function delete ($params) {
		
		try {
			if (ApineSession::is_logged_in()) {
				if (!ApineSession::logout()) {
					throw new ApineException('Token not found', 410);
				}
			} else {
				throw new ApineException('Unauthrorized', 401);
			}
		} catch (Exception $e) {
			throw new ApineException($e->getMessage(), $e->getCode(), $e);
		}
		
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
}