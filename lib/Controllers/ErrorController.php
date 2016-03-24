<?php
/**
 * Error Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\Application as Application;
use Apine\MVC as MVC;
use Apine\Core as Core;

class ErrorController extends MVC\Controller {
	
	public function __construct() {
		
		parent::__construct();
		
		if (Core\Request::is_api_call()) {
			$this->_view = new MVC\JSONView();
		}
		
	}
	
	public function badrequest ($a_exception = null) {
	
		return $this->custom(400, Application\ApplicationTranslator::translate('errors', '400'), $a_exception);
	
	}
	
	public function unauthorized ($a_exception = null) {
	
		return $this->custom(401, Application\ApplicationTranslator::translate('errors', '401'), $a_exception);
	
	}
	
	public function forbidden ($a_exception = null) {
	
		return $this->custom(403, Application\ApplicationTranslator::translate('errors', '403'), $a_exception);
	
	}
	
	public function notfound ($a_exception = null) {
		
		return $this->custom(404, Application\ApplicationTranslator::translate('errors', '404'), $a_exception);
		
	}
	
	public function methodnotallowed ($a_exception = null) {
	
		return $this->custom(405, Application\ApplicationTranslator::translate('errors', '405'), $a_exception);
	
	}
	
	public function notacceptable ($a_exception = null) {
	
		return $this->custom(406, Application\ApplicationTranslator::translate('errors', '406'), $a_exception);
	
	}
	
	public function gone ($a_exception = null) {
		
		return $this->custom(410, Application\ApplicationTranslator::translate('errors', '410'), $a_exception);
		
	}
	
	public function teapot ($a_exception = null) {
	
		return $this->custom(418, Application\ApplicationTranslator::translate('errors', '418'), $a_exception);
	
	}
	
	public function server ($a_exception = null) {
		
		return $this->custom(500, Application\ApplicationTranslator::translate('errors', '500'), $a_exception);
		
	}
	
	public function notimplemented ($a_exception = null) {
	
		return $this->custom(501, Application\ApplicationTranslator::translate('errors', '501'), $a_exception);
	
	}
	
	public function badgateway ($a_exception = null) {
	
		return $this->custom(502, Application\ApplicationTranslator::translate('errors', '502'), $a_exception);
	
	}
	
	public function unavailable ($a_exception = null) {
	
		return $this->custom(503, Application\ApplicationTranslator::translate('errors', '503'), $a_exception);
	
	}
	
	public function gatewaytimeout ($a_exception = null) {
	
		return $this->custom(504, Application\ApplicationTranslator::translate('errors', '504'), $a_exception);
	
	}
	
	public function custom ($a_code, $a_message, $a_exception = null) {
		
		$this->_view->set_param('code', $a_code);
		$this->_view->set_param('message', $a_message);
		
		if (Core\Request::is_api_call() || Core\Request::is_ajax()) {
			$this->_view->set_param('request', Core\Request::get()['request']);
		} else {
			$this->_view->set_title($a_message);
			$this->_view->set_view('error/error');
		}
		
		if ($a_exception !== null && !is_array($a_exception)) {
			$this->_view->set_param('file', $a_exception->getFile());
			$this->_view->set_param('line', $a_exception->getLine());
			$this->_view->set_param('trace', $a_exception->getTraceAsString());
		}
		
		if ($this->is_http_code($a_code)) {
			$this->_view->set_response_code($a_code);
		} else {
			$this->_view->set_response_code(500);
		}
		
		return $this->_view;
		
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
			case '406':
				$return = 'notacceptable';
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
			case '502':
				$return = 'badgateway';
				break;
			case '503':
				$return = 'unavailable';
				break;
			case '504':
				$return = 'gatewaytimeout';
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
			case '406':
			case '410':
			case '418':
			case '500':
			case '501':
			case '502':
			case '503':
			case '504':
				return true;
			default:
				return false;
		}
		
	}
}