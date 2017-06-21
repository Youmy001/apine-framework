<?php
/**
 * Reference Controllers
 * This script contains an reference controler for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

use Apine\Application\Application;
use Apine\Core\Request as Request;
use ReflectionClass;

/**
 * Basic Controller
 * Describes basics for user controllers
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
abstract class Controller {
	
	/**
	 * Controller View
	 * 
	 * @var View
     * @deprecated
	 */
	protected $_view;
	
	/**
	 * Construct the Controller
     *
     * @deprecated
	 */
	public function __construct() {
		
		if (Request::is_api_call() || Request::is_ajax()) {
			$this->_view = new JSONView();
		} else {
		    $viewClass = new ReflectionClass(Application::get_instance()->get_default_view());
			$this->_view = $viewClass->newInstance();
		}
		
	}
}
