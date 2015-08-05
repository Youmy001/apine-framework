<?php

class Request{
	
	private static $_instance;
	
	/**
	 * Session Request Type
	 * @var string
	 */
	private $request_type;
	
	/**
	 * Session Request Port
	 * @var string
	 */
	private $request_port;
	
	/**
	 * Is request from https protocol
	 * @var boolean
	 */
	private $request_ssl;
	
	/**
	 * Session API Call
	 * @var boolean
	 */
	private $api_call;
	
	private function __construct(){
		$this->request_type=$_SERVER['REQUEST_METHOD'];
		$this->request_port=$_SERVER['SERVER_PORT'];
		$this->request_ssl=(isset($_SERVER['HTTPS'])&&!empty($_SERVER['HTTPS']));
		$this->api_call=(isset($_GET['api']) && $_GET['api']==='api');
	}
	
	public static function get_instance(){
		if(!isset(self::$_instance)){
			self::$_instance = new static();
		}
		return self::$_instance;
	}
	
	/**
	 * Return the type of the current http request
	 * @return string
	 */
	public static function get_request_type(){
		return self::get_instance()->request_type;
	}
	
	/**
	 * Return the port used by the user in the current request
	 * @return string
	 */
	public static function get_request_port(){
		return self::get_instance()->request_port;
	}
	
	/**
	 * Checks if the request is made through the HTTPS protocol
	 * @return boolean
	 */
	public static function is_https(){
		return self::get_instance()->request_ssl;
	}
	
	/**
	 * Checks if the request is made to the API
	 * @return boolean
	 */
	public static function is_api_call(){
		$return=false;
		if(self::get_instance()->api_call==true){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a GET request or not
	 * @return boolean
	 */
	public static function is_get(){
		$return=false;
		if(self::get_instance()->request_type=="GET"){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a POST request or not
	 * @return boolean
	 */
	public static function is_post(){
		$return=false;
		if(self::get_instance()->request_type=="POST"){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a PUT request or not
	 * @return boolean
	 */
	public static function is_put(){
		$return=false;
		if(self::get_instance()->request_type=="PUT"){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a DELETE request or not
	 * @return boolean
	 */
	public static function is_delete(){
		$return=false;
		if(self::get_instance()->request_type=="DELETE"){
			$return=true;
		}
		return $return;
	}
}