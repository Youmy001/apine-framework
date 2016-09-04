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
use \Exception;

/**
 * Class ErrorController
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class ErrorController extends MVC\Controller {

    /**
     * HTTP Status handled by the controller
     *
     * @var array
     */
	private $errors = array(
			400 => ['title' => 'Bad Request', 'method' => 'badrequest'],
			401 => ['title' => 'Unauthorized', 'method' => 'unauthorized'],
			403 => ['title' => 'Forbidden', 'method' => 'forbidden'],
			404 => ['title' => 'Not Found', 'method' => 'notfound'],
			405 => ['title' => 'Method Not Allowed', 'method' => 'methodnotallowed'],
			406 => ['title' => 'Not Acceptable', 'method' => 'notacceptable'],
			410 => ['title' => 'Gone', 'method' => 'gone'],
			418 => ['title' => 'I\'m a Teapot', 'method' => 'teapot'],
			500 => ['title' => 'Internal Server Error', 'method' => 'server'],
			501 => ['title' => 'Not Implemented', 'method' => 'notimplemented'],
			502 => ['title' => 'Bad Gateway', 'method' => 'badgateway'],
			503 => ['title' => 'Service Unavailable', 'method' => 'unavailable'],
			504 => ['title' => 'Gateway Time-out', 'method' => 'gatewaytimeout']
	);

    /**
     * ErrorController constructor.
     */
	public function __construct() {
		
		parent::__construct();
		
		if (Core\Request::is_api_call()) {
			$this->_view = new MVC\JSONView();
		}
		
	}

    /**
     * Bad Request Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function badrequest (Exception $a_exception = null) {
		
		if (null == ($title = Application\Translator::translate('errors', '400'))) {
			$title = $this->errors[400]['title'];
		}
		
		return $this->custom(400, $title, $a_exception);
	
	}

    /**
     * Authorization Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function unauthorized (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '401'))) {
			$title = $this->errors[401]['title'];
		}
		
		return $this->custom(401, $title, $a_exception);
	
	}

    /**
     * Permission Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function forbidden (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '403'))) {
			$title = $this->errors[403]['title'];
		}
		
		return $this->custom(403, $title, $a_exception);
	
	}

    /**
     * Not Found Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function notfound (Exception $a_exception = null) {
		
		if (null == ($title = Application\Translator::translate('errors', '404'))) {
			$title = $this->errors[404]['title'];
		}
		
		return $this->custom(404, $title, $a_exception);
		
	}

    /**
     * Request Method Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function methodnotallowed (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '405'))) {
			$title = $this->errors[405]['title'];
		}
		
		return $this->custom(405, $title, $a_exception);
	
	}

    /**
     * Accepted Content Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function notacceptable (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '406'))) {
			$title = $this->errors[406]['title'];
		}
		
		return $this->custom(406, $title, $a_exception);
	
	}

    /**
     * Gone Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function gone (Exception $a_exception = null) {
		
		if (null == ($title = Application\Translator::translate('errors', '410'))) {
			$title = $this->errors[410]['title'];
		}
		
		return $this->custom(410, $title, $a_exception);
		
	}

    /**
     * Teapot Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function teapot (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '418'))) {
			$title = $this->errors[418]['title'];
		}
		
		return $this->custom(418, $title, $a_exception);
	
	}

    /**
     * Server Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function server (Exception $a_exception = null) {
		
		if (null == ($title = Application\Translator::translate('errors', '500'))) {
			$title = $this->errors[500]['title'];
		}
		
		return $this->custom(500, $title, $a_exception);
		
	}

    /**
     * Implementation Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function notimplemented (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '501'))) {
			$title = $this->errors[501]['title'];
		}
		
		return $this->custom(501, $title, $a_exception);
	
	}

    /**
     * Gateway Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function badgateway (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '502'))) {
			$title = $this->errors[502]['title'];
		}
		
		return $this->custom(502, $title, $a_exception);
	
	}

    /**
     * Service Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function unavailable (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '503'))) {
			$title = $this->errors[503]['title'];
		}
		
		return $this->custom(503, $title, $a_exception);
	
	}

    /**
     * Gateway Timeout Error
     *
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function gatewaytimeout (Exception $a_exception = null) {
	
		if (null == ($title = Application\Translator::translate('errors', '504'))) {
			$title = $this->errors[504]['title'];
		}
		
		return $this->custom(504, $title, $a_exception);
	
	}

    /**
     * Error view generation
     *
     * @param string|integer $a_code
     * @param string $a_message
     * @param Exception $a_exception
     * @return MVC\View
     */
	public function custom ($a_code, $a_message, Exception $a_exception = null) {
		
		$this->_view->set_param('code', $a_code);
		$this->_view->set_param('message', $a_message);
		
		if (Core\Request::is_api_call() || Core\Request::is_ajax()) {
			$this->_view->set_param('request', Core\Request::get()['request']);
		} else {
			$this->_view->set_title($a_message);
			$this->_view->set_view('error');
		}
		
		if ($a_exception !== null && !is_array($a_exception)) {
			$this->_view->set_param('file', $a_exception->getFile());
			$this->_view->set_param('line', $a_exception->getLine());
			
			if (Application\Application::get_instance()->get_mode() === APINE_MODE_DEVELOPMENT) {
				$this->_view->set_param('trace', $a_exception->getTraceAsString());
			}
		}
		
		if ($this->is_http_code($a_code)) {
			$this->_view->set_response_code($a_code);
		} else {
			$this->_view->set_response_code(500);
		}
		return $this->_view;
		
	}

    /**
     * Get the appropriate method for a status code
     *
     * @param string|integer $a_code
     * @return boolean
     */
	public function method_for_code ($a_code) {
		
		return (isset($this->errors[$a_code])) ? $this->errors[$a_code]['method'] : false;
		
	}

    /**
     * Verify a status code is handled by the controller
     * 
     * @param string|integer $a_code
     * @return boolean
     */
	private function is_http_code ($a_code) {
		
		return (isset($this->errors[$a_code]));
		
	}
}