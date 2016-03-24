<?php
/**
 * Version Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\Core as Core;
use Apine\MVC as MVC;
use Apine\Exception\GenericException as GenericException;

class VersionController extends MVC\Controller implements MVC\APIActionsInterface {
	
	public function index () {
		
		$this->_view->set_view('version/version');
		
		return $this->_view;
		
	}
	
	public function get ($params) {
		
		//$this->_view = new ApineJSONView();
		
		$array['application'] = array('version' => Core\Version::application());
		$array['apine_framework'] = array('version' => Core\Version::framework());
		
		$this->_view->set_json_file($array);
		$this->_view->set_response_code(200);
		
		return $this->_view;
		
	}
	
	public function post ($params) {
	
		throw new GenericException("Method Not Allowed", 405);
		
	}
	
	public function put ($params) {
		
		throw new GenericException("Method Not Allowed", 405);
	
	}
	
	public function delete ($params) {
		
		throw new GenericException("Method Not Allowed", 405);
	
	}
	
}