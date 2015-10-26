<?php

class ErrorController extends Controller {
	
	public function __construct() {
		
		parent::__construct();
		
		if (ApineRequest::is_api_call()) {
			$this->_view = new ApineJSONView();
		}
		
	}
	
	public function badrequest ($a_exception = null) {
	
		$this->custom(400, 'Bad Request', $a_exception);
	
	}
	
	public function unauthorized ($a_exception = null) {
	
		$this->custom(401, 'Unauthorized', $a_exception);
	
	}
	
	public function forbidden ($a_exception = null) {
	
		$this->custom(403, 'Forbidden', $a_exception);
	
	}
	
	public function notfound ($a_exception = null) {
		
		$this->custom(404, 'Not Found', $a_exception);
		
	}
	
	public function methodnotallowed ($a_exception = null) {
	
		$this->custom(405, 'Method Not Allowed', $a_exception);
	
	}
	
	public function gone ($a_exception = null) {
		
		$this->custom(410, 'Gone', $a_exception);
		
	}
	
	public function teapot ($a_exception = null) {
	
		$this->custom(418, 'I\'m a teapot', $a_exception);
	
	}
	
	public function server ($a_exception = null) {
		
		$this->custom(500, 'Internal Server Error', $a_exception);
		
	}
	
	public function notimplemented ($a_exception = null) {
	
		$this->custom(501, 'Not Implemented', $a_exception);
	
	}
	
	public function unavailable ($a_exception = null) {
	
		$this->custom(503, 'Service Unavailable', $a_exception);
	
	}
	
	public function custom ($a_code, $a_message, $a_exception = null) {
		
		$this->_view->set_param('code', $a_code);
		$this->_view->set_param('message', $a_message);
		
		if (ApineRequest::is_api_call()) {
			$this->_view->set_param('request', Request::get()['request']);
		} else {
			$this->_view->set_title($a_message);
			$this->_view->set_view('error/error');
		}
		
		if ($a_exception !== null) {
			$this->_view->set_param('file', $a_exception->getFile());
			$this->_view->set_param('line', $a_exception->getLine());
			$this->_view->set_param('trace', $a_exception->getTraceAsString());
		}
		
		if ($this->is_http_code($a_code)) {
			$this->_view->set_response_code($a_code);
		} else {
			$this->_view->set_response_code(500);
		}
		
		$this->_view->draw();
		
	}
	
	public function method_for_code ($a_code) {
		
		switch ($a_code) {
			case '400':
				$return = 'badrequest';
				break;
			case '401':
				$return = 'unauthorized';
				break;
			case '403':
				$return = 'forbidden';
				break;
			case '404':
				$return = 'notfound';
				break;
			case '405':
				$return = 'methodnotallowed';
				break;
			case '410':
				$return = 'gone';
				break;
			case '418':
				$return = 'teapot';
				break;
			case '500':
				$return = 'server';
				break;
			case '501':
				$return = 'notimplemented';
				break;
			case '503':
				$return = 'unavailable';
				break;
			default:
				$return = false;
		}
		
		return $return;
		
	}
	
	private function is_http_code ($a_code) {
		
		switch ($a_code) {
			case '400':
			case '401':
			case '403':
			case '404':
			case '405':
			case '410':
			case '418':
			case '500':
			case '501':
			case '503':
				return true;
			default:
				return false;
		}
		
	}
}