<?php
/**
 * Authentication Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

class AuthController extends APIController {
	
	public function get ($params) {
		
		if (count($params)==2) {
			
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
		
		throw new ApineException('Not Implemented', 501);
		
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
		
	}
	
}