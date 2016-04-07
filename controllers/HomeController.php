<?php
/**
 * Home Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\User;

use Apine\MVC;

class HomeController extends MVC\Controller {
	
	public function index ($params) {
		
		$this->_view->set_title('Home');
		$this->_view->set_view('home/index');
		
		$this->_view->set_response_code(200);
		
		/*var_dump(url_helper()->path('home'));
		var_dump(application_config()->get('application', 'title'));
		var_dump(session_manager()->is_logged_in());*/
		
		return $this->_view;
		
	}
	
	public function about () {
		
		$this->_view->set_title('About');
		$this->_view->set_view('home/about');
	
		$this->_view->set_response_code(200);
		
		return $this->_view;
		
	}
	
}