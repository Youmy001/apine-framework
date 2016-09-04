<?php
/**
 * Home Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\MVC as MVC;

/**
 * Class HomeController
 *
 * Placeholder controller for a home page
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class HomeController extends MVC\Controller {

    /**
     * Default Action
     *
     * @return MVC\HTMLView
     */
	public function index () {
		
		$this->_view->set_title('Home');
		$this->_view->set_view('home');
		
		return $this->_view;
		
	}
}