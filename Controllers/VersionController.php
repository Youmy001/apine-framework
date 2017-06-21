<?php
/**
 * Version Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\Application\Application;
use Apine\Exception\GenericException;
use Apine\MVC\APIActionsInterface;
use Apine\MVC\Controller;
use Apine\MVC\HTMLView;
use Apine\MVC\JSONView;

/**
 * Class VersionController
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class VersionController extends Controller implements APIActionsInterface {

    /**
     * Default Action
     *
     * @return HTMLView
     */
    public function index () {

        $view = new HTMLView();
		
		$view->set_view('version');
		$view->set_title('Version');
		
		return $view;
		
	}

    /**
     * HTTP GET action
     *
     * @see APIActionsInterface
     * @return JSONView
     */
	public function get ($params) {

	    $view = new JSONView();
		
		$version = Application::get_instance()->get_version();
		
		$array['application'] = array('version' => $version->application());
		$array['apine_framework'] = array('version' => $version->framework());
		
		$view->set_json_file($array);
		$view->set_response_code(200);
		
		return $view;
		
	}

    /**
     * HTTP POST action
     *
     * @see APIActionsInterface
     * @throws GenericException
     */
	public function post ($params) {
	
		throw new GenericException("Method Not Allowed", 405);
		
	}

    /**
     * HTTP PUT action
     *
     * @see APIActionsInterface
     * @throws GenericException
     */
	public function put ($params) {
		
		throw new GenericException("Method Not Allowed", 405);
	
	}

    /**
     * HTTP DELETE action
     *
     * @see APIActionsInterface
     * @throws GenericException
     */
	public function delete ($params) {
		
		throw new GenericException("Method Not Allowed", 405);
	
	}
	
}