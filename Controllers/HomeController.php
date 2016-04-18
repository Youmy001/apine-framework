<?php
/**
 * Home Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\MVC as MVC;

class HomeController extends MVC\Controller {
	
	public function index () {
		
		$this->_view->set_title('Home');
		$this->_view->set_view('index');
		
		return $this->_view;
		
	}
}