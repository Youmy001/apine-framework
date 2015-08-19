<?php

class ErrorController extends Controller {
	
	public function notfound() {
		
		$this->_view->set_title('Not Found');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(404);
		
		$this->_view->set_param('error_code', 404);
		$this->_view->set_param('error_message', 'Not Found');
		
		$this->_view->draw();
		
	}
	
	public function forbidden() {
		
		$this->_view->set_title('Forbidden');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(403);
		
		$this->_view->set_param('error_code', 403);
		$this->_view->set_param('error_message', 'Forbidden');
		
		$this->_view->draw();
		
	}
	
	public function gone() {
		
		$this->_view->set_title('Gone');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(410);
		
		$this->_view->set_param('error_code', 410);
		$this->_view->set_param('error_message', 'Gone');
		
		$this->_view->draw();
		
	}
	
	public function unauthorized() {
		
		$this->_view->set_title('Unauthorized');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(401);
		
		$this->_view->set_param('error_code', 401);
		$this->_view->set_param('error_message', 'Unauthorized');
		
		$this->_view->draw();
		
	}
	
	public function methodnotallowed() {
		
		$this->_view->set_title('Method Not Allowed');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(405);
		
		$this->_view->set_param('error_code', 405);
		$this->_view->set_param('error_message', 'Method Not Allowed');
		
		$this->_view->draw();
		
	}
	
	public function server() {
		
		$this->_view->set_title('Internal Server Error');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(500);
		
		$this->_view->set_param('error_code', 500);
		$this->_view->set_param('error_message', 'Internal Server Error');
		
		$this->_view->draw();
		
	}
	
	public function badrequest() {
		
		$this->_view->set_title('Bad Request');
		$this->_view->set_view('error/error');
		$this->_view->set_response_code(400);
		
		$this->_view->set_param('error_code', 400);
		$this->_view->set_param('error_message', 'Bad Request');
		
		$this->_view->draw();
		
	}
	
	public function custom($code,$message) {
		
		$this->_view->set_title($message);
		$this->_view->set_view('error/error');
		$this->_view->set_response_code($code);
		
		$this->_view->set_param('error_code', $code);
		$this->_view->set_param('error_message', $message);
		
		$this->_view->draw();
		
	}
}