<?php
require_once('lib/mvc/AbstractController.php');

class SessionController extends AbstractController{
	
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
					}else{
						throw new Exception("0");
					}
				}else{
					throw new Exception("1");
				}
				throw new Exception("2");
			}catch(Exception $e){
				//header("Location: " . URL_Helper::path("login/error/" . $e->getMessage()));
				switch($e->getMessage()){
					case '0':
						$message = LOGIN_ERROR_0;
						break;
					case '1':
						$message = LOGIN_ERROR_1;
						break;
					case '2':
					default:
						$message = LOGIN_ERROR_2;
				}
				$this->_view->set_param('error_code',true);
				$this->_view->set_param('error_message', $message);
			}
			
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
			/* TODO User registration */
		}
		$this->_view->set_title('Sign Up');
		$this->_view->set_view('session/register');
		$this->_view->set_response_code(200);
		$this->_view->draw();
	}
	
	public function restore(){
		
	}
	
	public function redirect(){
		
	}
}