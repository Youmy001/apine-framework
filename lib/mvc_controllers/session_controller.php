<?php

class SessionController extends Controller {
	
	public function login ($params) {
		
		if (Request::is_post()) {
			
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
				$permanent = $params['perm'];
			} else {
				$permanent = 'off';
			}
			
			try {
				
				if ((isset($user) && isset($pwd)) && (!ApineSession::is_logged_in())) {
					
					if (ApineSession::login($user, $pwd)) {
						
						if ($permanent == 'on') {
							ApineSession::make_permanent();
						}
						
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
				$message = ApineTranslator::translate('errors', 'login_invalid_username');
			} catch (Exception $e) {
				//var_dump($e);
				//$message = 'An unknown error occured when sending data to the server. Please try again later.';
				$message = ApineTranslator::translate('errors', 'form_invalid');
			}
			
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		
		$this->_view->set_title(ApineTranslator::translate('login', 'title'));
		//$this->_view->set_title('Login');
		$this->_view->set_view('session/login');
		$this->_view->set_response_code(200);
		$this->_view->draw();
		
	}
	
	public function logout () {
		
		if (ApineSession::is_logged_in()) {
			ApineSession::logout();
		}
		
		header("Location: " . URL_Helper::path("login"));
		
	}
	
	public function register ($params) {
		
		if (Request::is_post()&&!ApineSession::is_logged_in()) {
			
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
			
			/*var_dump(filter_var($email,FILTER_VALIDATE_EMAIL));
			var_dump(preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD', $email));
			die();*/
			
			try {
				
				// Make sure required data are provided
				if ((isset($user) && isset($pwd) && isset($email)) && (!ApineSession::is_logged_in())) {
					
					// Verify the user name isn't already in use
					if (ApineUserFactory::is_name_exist($user)) {
						throw new Exception("4"); // Already used username
					}
					
					// Verify both passwords are identical
					if (($pwd === $pwdconfirm)) {
						$encoded_pwd = Encryption::hash_password($pwd, $user);
					} else {
						throw new Exception("3"); // Wrong password
					}
					
					// Verify Email format if it exists
					/*if(isset($email) && !empty($email) && preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD', $email) != 1){
						throw new Exception("2"); // Misformated Email
					}*/
					
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
					$new_user->set_type(SESSION_USER);
					
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
						//$message='The email address is already taken.';
						$message = ApineTranslator::translate('errors', 'register_taken_email');
						break;
					case 4:
						//$message='The username is already taken by an user.';
						$message = ApineTranslator::translate('errors', 'register_taken_username');
						break;
					case 3:
						//$message='The password does not match the confirmation.';
						$message = ApineTranslator::translate('errors', 'register_invalid_password');
						break;
					case 2:
						//$message='The email address is not valid.';
						$message = ApineTranslator::translate('errors', 'register_invalid_email');
						break;
					case 0:
					default:
						//$message='An unknown error occured when sending data to the server. Please try again later.';
						$message = ApineTranslator::translate('errors', 'form_invalid');
				}
				
			}
			
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		
		$this->_view->set_title(ApineTranslator::translate('register', 'title'));
		//$this->_view->set_title("Sign In");
		$this->_view->set_view('session/register');
		$this->_view->set_response_code(200);
		$this->_view->draw();
		
	}
}