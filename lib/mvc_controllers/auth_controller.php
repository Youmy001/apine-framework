<?php

class AuthController extends APIController {
	
	public function get ($params) {
		
		if (count($params)==2) {
			
			$auth_username = get_args()[0];
			$auth_password = base64_decode(get_args()[1]);
			
			try {
				if (!ApineSession::is_logged_in()) {
					if (ApineSession::login($auth_username, $auth_password)) {
						$this->_view->set_param('username', $auth_username);
						$this->_view->set_param('token', ApineSession::get_session_identifier()->get_token());
						$this->_view->set_param('origin', ApineSession::get_session_identifier()->get_origin());
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
		
	}
	
	public function put ($params) {
		
		throw new ApineException("Method Not Allowed", 405);
		
	}
	
	public function delete ($params) {
		
	}
	
}