<?php

class HomeController extends ApineController {
	
	public function index () {
		
		$this->_view->set_title('Home');
		$this->_view->set_view('home/index');
		
		$this->_view->set_response_code(200);
		$this->_view->draw();
		
	}
	
	public function about () {
		
		$this->_view->set_title('About');
		$this->_view->set_view('home/about');
	
		$this->_view->set_response_code(200);
		$this->_view->draw();
		
	}
	
}