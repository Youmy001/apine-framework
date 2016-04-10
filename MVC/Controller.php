<?php
/**
 * Reference Controllers
 * This script contains an reference controler for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

use Apine\Core\Request as Request;

/**
 * Basic Controller
 * Describes basics for user controllers
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
abstract class Controller {
	
	/**
	 * Controller View
	 * 
	 * @var ApineView
	 */
	protected $_view;
	
	/**
	 * Construct the Controller
	 */
	public function __construct() {
		
		if (Request::is_api_call() || Request::is_ajax()) {
			$this->_view = new JSONView();
		} else {
			$this->_view = new HTMLView();
		}
		
	}
}
