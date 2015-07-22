<?php

class SessionController extends Controller{
	
	public function login($params){
		
		if(session()->is_post()){
			if(isset($params['user']))
				$user = htmlspecialchars($params['user']);
			if(isset($params['pwd']))
				$pwd = htmlspecialchars($params['pwd']);
			if(isset($params['redirect']))
				$redirect = $params['redirect'];
			if(isset($params['perm'])){
				$permanent = $params['perm'];
			}else{
				$permanent = 'off';
			}
			
			try{
				if((isset($user) && isset($pwd)) && (!session()->is_logged_in())){
					if(session()->login($user, $pwd)){
						if($permanent == 'on'){
							session()->make_permanent();
						}
						if(isset($redirect) && $redirect != ""){
							header("Location: " . $redirect);
						}else{
							header("Location: " . URL_Helper::path(""));
							die();
						}
						die();
					}
				}
				$message = 'Either the the username or the password is not valid. Please try again later.';
			}catch(Exception $e){
				$message = 'An unknown error occured when sending data to the server. Please try again later.';
			}
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		
		$this->_view->set_title('Login');
		$this->_view->set_view('session/login');
		$this->_view->set_response_code(200);
		$this->_view->draw();
	}
	
	public function logout(){
		if(session()->is_logged_in()){
			session()->logout();
		}
		header("Location: " . URL_Helper::path("login"));
	}
	
	public function register(){
		if(session()->is_post()&&!session()->is_logged_in()){
			
			if(isset($_POST['user']))
				$user = htmlspecialchars($_POST['user']);
			if(isset($_POST['pwd']))
				$pwd = htmlspecialchars($_POST['pwd']);
			if(isset($_POST['redirect']))
				$redirect = $_POST['redirect'];
			if(isset($_POST['pwd_confirm']))
				$pwdconfirm = htmlspecialchars($_POST['pwd_confirm']);
			if(isset($_POST['email']))
				$email = htmlspecialchars($_POST['email']);
			
			try{
				// Make sure required data are provided
				if((isset($user) && isset($pwd) && isset($email)) && (!session()->is_logged_in())){
					// Verify the user name isn't already in use
					if(ApineUserFactory::is_name_exist($user)){
						throw new Exception("4"); // Already used username
					}
					// Verify both passwords are identical
					if(($pwd === $pwdconfirm)){
						$encoded_pwd = hash('sha256', $pwd);
					}else{
						throw new Exception("3"); // Wrong password
					}
					// Verify Email format if it exists
					if(isset($email) && !empty($email) && preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD', $_POST['email']) != 1){
						throw new Exception("2"); // Misformated Email
					}
					if(ApineUserFactory::is_email_exist($email)){
						throw new Exception("7"); // Already used email
					}
					// Create new empty user
					$new_user = new ApineUser();
					// Populate new user
					$new_user->set_username($user);
					if(isset($realname)){
						$new_user->set_realname($realname);
					}
					$new_user->set_password($encoded_pwd);
					if(!empty($email)){
						$new_user->set_email_address($email);
					}
					$new_user->set_type(SESSION_USER);
					// Write new user in database
					$new_user->save();
					
					if(isset($redirect) && $redirect != ""){
						header("Location: " . $redirect);
					}else{
						header("Location: " . URL_Helper::path("login"));
						die();
					}
					die();
				}
				throw new Exception("0"); // Unknown Error
			}catch (Exception $e){
				switch($e->getMessage()){
					case 7:
						$message='The email address is already taken.';
						break;
					case 4:
						$message='The username is already taken by an user.';
						break;
					case 3:
						$message='The password does not match the confirmation.';
						break;
					case 2:
						$message='The email address is not valid.';
						break;
					case 0:
					default:
						$message='An unknown error occured when sending data to the server. Please try again later.';
				}
			}
			$this->_view->set_param('error_code',true);
			$this->_view->set_param('error_message', $message);
		}
		$this->_view->set_title('Sign Up');
		$this->_view->set_view('session/register');
		$this->_view->set_response_code(200);
		$this->_view->draw();
	}
	
	public function restore(){
		if(session()->is_post()&&!session()->is_logged_in()){
			/* TODO Password Restoration */
		}
		$this->_view->set_title('Reset Password');
		$this->_view->set_view('session/reset');
		$this->_view->set_response_code(200);
		$this->_view->draw();
	}
	
	public function redirect(){
		
	}
}