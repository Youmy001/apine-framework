<?php
/**
 * Error Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\Application\Application;
use Apine\Application\Translator;
use Apine\Core\Request;
use Apine\MVC\Controller;
use Apine\MVC\JSONView;
use Apine\MVC\View;
use \Exception;
use ReflectionClass;

/**
 * Class ErrorController
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class ErrorController extends Controller {

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
     * Bad Request Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function badrequest (Exception $a_exception = null) {
		
		if (null == ($title = Translator::translate('errors', '400'))) {
			$title = $this->errors[400]['title'];
		}
		
		return $this->custom(400, $title, $a_exception);
	
	}

    /**
     * Authorization Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function unauthorized (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '401'))) {
			$title = $this->errors[401]['title'];
		}
		
		return $this->custom(401, $title, $a_exception);
	
	}

    /**
     * Permission Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function forbidden (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '403'))) {
			$title = $this->errors[403]['title'];
		}
		
		return $this->custom(403, $title, $a_exception);
	
	}

    /**
     * Not Found Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function notfound (Exception $a_exception = null) {
		
		if (null == ($title = Translator::translate('errors', '404'))) {
			$title = $this->errors[404]['title'];
		}
		
		return $this->custom(404, $title, $a_exception);
		
	}

    /**
     * Request Method Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function methodnotallowed (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '405'))) {
			$title = $this->errors[405]['title'];
		}
		
		return $this->custom(405, $title, $a_exception);
	
	}

    /**
     * Accepted Content Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function notacceptable (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '406'))) {
			$title = $this->errors[406]['title'];
		}
		
		return $this->custom(406, $title, $a_exception);
	
	}

    /**
     * Gone Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function gone (Exception $a_exception = null) {
		
		if (null == ($title = Translator::translate('errors', '410'))) {
			$title = $this->errors[410]['title'];
		}
		
		return $this->custom(410, $title, $a_exception);
		
	}

    /**
     * Teapot Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function teapot (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '418'))) {
			$title = $this->errors[418]['title'];
		}
		
		return $this->custom(418, $title, $a_exception);
	
	}

    /**
     * Server Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function server (Exception $a_exception = null) {
		
		if (null == ($title = Translator::translate('errors', '500'))) {
			$title = $this->errors[500]['title'];
		}
		
		return $this->custom(500, $title, $a_exception);
		
	}

    /**
     * Implementation Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function notimplemented (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '501'))) {
			$title = $this->errors[501]['title'];
		}
		
		return $this->custom(501, $title, $a_exception);
	
	}

    /**
     * Gateway Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function badgateway (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '502'))) {
			$title = $this->errors[502]['title'];
		}
		
		return $this->custom(502, $title, $a_exception);
	
	}

    /**
     * Service Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function unavailable (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '503'))) {
			$title = $this->errors[503]['title'];
		}
		
		return $this->custom(503, $title, $a_exception);
	
	}

    /**
     * Gateway Timeout Error
     *
     * @param Exception $a_exception
     * @return View
     */
	public function gatewaytimeout (Exception $a_exception = null) {
	
		if (null == ($title = Translator::translate('errors', '504'))) {
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
     * @return View
     */
	public function custom ($a_code, $a_message, Exception $a_exception = null) {

        if (Request::is_api_call() || Request::is_ajax()) {
            $view = new JSONView();
        } else {
            $viewClass = new ReflectionClass(Application::get_instance()->get_default_view());
            $view = $viewClass->newInstance();
        }

        $view->set_param('code', $a_code);
        $view->set_param('message', $a_message);

        if (Request::is_api_call() || Request::is_ajax()) {
            $view->set_param('request', Request::get()['request']);
        } else {
            $view->set_title($a_message);
            $view->set_view('error');
        }

        if ($a_exception !== null && !is_array($a_exception)) {
            $view->set_param('file', $a_exception->getFile());
            $view->set_param('line', $a_exception->getLine());

            if (Application::get_instance()->get_mode() === APINE_MODE_DEVELOPMENT) {
                if (Request::is_api_call() || Request::is_ajax()) {
                    $view->set_param('trace', $a_exception->getTrace());
                } else {
                    $view->set_param('trace', $a_exception->getTraceAsString());
                }

                $view->set_param('exception', $this->getInnerException($a_exception != null ? $a_exception->getPrevious() : null));
            }
        }

		if ($this->is_http_code($a_code)) {
			$view->set_response_code($a_code);
		} else {
			$view->set_response_code(500);
		}
		return $view;
		
	}

	private function getInnerException($exception) {
	    if ($exception == null) {
	        return null;
        }

	    $data = [];
        $data['code'] = $exception->getCode();
        $data['message'] = $exception->getMessage();
        $data['file'] = $exception->getFile();
        $data['line'] = $exception->getLine();
        $data['trace'] = $exception->getTrace();
        $data['exception'] = $this->getInnerException($exception->getPrevious());

	    return $data;
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