<?php
require_once('lib/mvc/View.php');

abstract class AbstractController{
	
	protected $_view;
	
	public function __construct(){
		$this->_view=new HTMLView();
	}
}