<?php
/**
 * Version Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

class VersionController extends ApineController implements ApineAPIActions {
	
	public function index () {
		
		$this->_view->set_view('version/version');
		
		return $this->_view;
		
	}
	
	public function get ($params) {
		
		$this->_view = new ApineJSONView();
		
		$array['application'] = array('version' => ApineVersion::application());
		$array['apine_framework'] = array('version' => ApineVersion::framework());
		
		$this->_view->set_json_file($array);
		$this->_view->set_response_code(200);
		
		return $this->_view;
		
	}
	
	public function post ($params) {
	
		throw new ApineException("Method Not Allowed", 405);
		
	}
	
	public function put ($params) {
		
		throw new ApineException("Method Not Allowed", 405);
	
	}
	
	public function delete ($params) {
		
		throw new ApineException("Method Not Allowed", 405);
	
	}
	
}