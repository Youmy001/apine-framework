<?php
/**
 * Reference Controllers
 * This script contains an reference controler for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

/**
 * Basic API Controller
 * Describes basics for user API controllers
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
abstract class APIController implements APIActionsInterface {

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

		$this->_view = new JSONView();

	}
}