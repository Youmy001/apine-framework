<?php

interface APIActions {

	public function post($params);

	public function get($params);

	public function put($params);

	public function delete($params);
}

abstract class Controller{
	
	protected $_view;
	
	public function __construct(){
		$this->_view=new HTMLView();
	}
}

abstract class APIController extends Controller implements APIActions{
	
	public function __construct(){
		$this->_view=new JSONView();
	}
}