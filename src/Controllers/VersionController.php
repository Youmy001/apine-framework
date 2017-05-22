<?php
/**
 * Version Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\Application\Application;
use Apine\MVC as MVC;
use Apine\Exception\GenericException as GenericException;

/**
 * Class VersionController
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class VersionController extends MVC\Controller implements MVC\APIActionsInterface {

    /**
     * Default Action
     *
     * @return MVC\HTMLView
     */
    public function index () {
		
		$this->_view->set_view('version');
		$this->_view->set_title('Version');
		
		return $this->_view;
		
	}

    /**
     * HTTP GET action
     *
     * @see MVC\APIActionsInterface
     * @return MVC\JSONView
     */
	public function get ($params) {
		
		$version = Application::get_instance()->get_version();
		
		$array['application'] = array('version' => $version->application());
		$array['apine_framework'] = array('version' => $version->framework());
		
		$this->_view->set_json_file($array);
		$this->_view->set_response_code(200);
		
		return $this->_view;
		
	}

    /**
     * HTTP POST action
     *
     * @see MVC\APIActionsInterface
     * @throws GenericException
     */
	public function post ($params) {
	
		throw new GenericException("Method Not Allowed", 405);
		
	}

    /**
     * HTTP PUT action
     *
     * @see MVC\APIActionsInterface
     * @throws GenericException
     */
	public function put ($params) {
		
		throw new GenericException("Method Not Allowed", 405);
	
	}

    /**
     * HTTP DELETE action
     *
     * @see MVC\APIActionsInterface
     * @throws GenericException
     */
	public function delete ($params) {
		
		throw new GenericException("Method Not Allowed", 405);
	
	}
	
}