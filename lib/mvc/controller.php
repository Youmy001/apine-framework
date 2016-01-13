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
interface ApineAPIActions {

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
abstract class ApineController {
	
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
		
		if (ApineRequest::is_api_call() || ApineRequest::is_ajax()) {
			$this->_view=new ApineJSONView();
		} else {
			$this->_view=new ApineHTMLView();
		}
		
	}
}

/**
 * Basic API Controller
 * Describes basics for user API controllers
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
abstract class ApineAPIController implements ApineAPIActions {
	
	/**
	 * Controller ApineView
	 *
	 * @var ApineJSONView
	 */
	protected $_view;
	
	/**
	 * Construct the API Controller
	 */
	public function __construct() {
		
		$this->_view=new ApineJSONView();
		
	}
}
