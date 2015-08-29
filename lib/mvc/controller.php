<?php
/**
 * Reference Controllers
 * This script contains an reference controler for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * API Actions Interface
 * Interface for mandatory actions in API controllers
 */
interface APIActions {

	public function post($params);

	public function get($params);

	public function put($params);

	public function delete($params);
}

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
	 * @var View
	 */
	protected $_view;
	
	/**
	 * Construct the Controller
	 */
	public function __construct() {
		
		$this->_view=new HTMLView();
		
	}
}

/**
 * Basic API Controller
 * Describes basics for user API controllers
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
abstract class APIController extends Controller implements APIActions {
	
	/**
	 * Construct the API Controller
	 */
	public function __construct() {
		
		$this->_view=new JSONView();
		
	}
}