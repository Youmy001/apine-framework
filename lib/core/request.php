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
	
	private $get;
	
	private $post;
	
	private $files;
	
	private $request;
	
	private $server;
	
	public $session;
	
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
		
		$this->get=$_GET;
		$this->post=$_POST;
		$this->files=$_FILES;
		$this->request=$_REQUEST;
		$this->server=$_SERVER;
		$this->session=&$_SESSION;
		
		$_GET=null;
		$_POST=null;
		$_FILES=null;
		$_REQUEST=null;
		//$_SERVER=null;
		
		foreach ($this->post as $key=>$value){
			$this->post[$key]=filter_var($value,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}
		
		// Format Files Array
		if(is_array($this->files)&&!empty($this->files)){
			$first=reset($this->files);
			if(isset($first['name']) && $count($first['name'])>1){
				$this->files=self::format_files_array($this->files);
			}
		}
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
	
	public static function get(){
		return self::get_instance()->get;
	}
	
	public static function post(){
		return self::get_instance()->post;
	}
	
	public static function files(){
		return self::get_instance()->files;
	}
	
	public static function request(){
		return self::get_instance()->request;
	}
	
	public static function server(){
		return self::get_instance()->server;
	}
	
	private static function format_files_array(Array $files){
		$result=array();
		
		foreach ($files as $key1 => $value1){
			foreach ($value1 as $key2 => $value2){
				$result[$key2][$key1]=$value2;
			}
		}
		return $result;
	}
}