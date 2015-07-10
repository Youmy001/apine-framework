<?php
require_once('lib/mvc/AbstractController.php');

class HomeController extends AbstractController{
	
	public function index(){
		$this->_view->set_title('Home');
		$this->_view->set_view('home/index');
		$this->_view->set_layout('home');
		
		$this->_view->set_response_code(200);
		$this->_view->draw();
	}
}