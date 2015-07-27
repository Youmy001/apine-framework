<?php

abstract class Controller{
	
	protected $_view;
	
	public function __construct(){
		$this->_view=new HTMLView();
	}
}

abstract class APIController extends Controller{
	
	public function __construct(){
		$this->_view=new JSONView();
	}
}