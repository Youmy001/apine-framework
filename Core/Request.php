<?php
/**
 * Request Management
 * This script contains an helper to handle request information
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

/**
 * Request Management Tool
 * 
 * Handle information from the request and user inputs
 */
final class Request {
	
	/**
	 * Instance of the Request
	 * Singleton Implementation
	 * 
	 * @var ApineRequest
	 */
	private static $_instance;
	
	/**
	 * Session Request Type
	 * 
	 * @var string
	 */
	private $request_type;
	
	/**
	 * Session Request Port
	 * 
	 * @var string
	 */
	private $request_port;
	
	/**
	 * Is request from https protocol
	 * 
	 * @var boolean
	 */
	private $request_ssl;
	
	/**
	 * Get method inputs
	 * 
	 * @var array
	 */
	private $get;
	
	/**
	 * Post method inputs
	 * 
	 * @var array
	 */
	private $post;
	
	/**
	 * Uploaded files input information
	 * 
	 * @var array
	 */
	private $files;
	
	/**
	 * Raw Request Body
	 * 
	 *  @var string
	 */
	private $request_body;
	
	/**
	 * Request information
	 * 
	 * @var array
	 */
	private $request;
	
	/**
	 * Server information
	 * 
	 * @var array
	 */
	private $server;
	
	/**
	 * Session information
	 * 
	 * @var array
	 */
	public $session;
	
	/**
	 * Session API Call
	 * 
	 * @var boolean
	 */
	private $api_call;
	
	/**
	 * Session AJAX Call
	 * 
	 * @var boolean
	 */
	private $is_ajax;
	
	/**
	 * Headers received
	 * 
	 * @var string[]
	 */
	private $request_headers;
	
	/**
	 * Construct the Request Management handler
	 * 
	 * Extract information from the request and clean user inputs 
	 */
	private function __construct () {
		
		$this->request_type	= $_SERVER['REQUEST_METHOD'];
		$this->request_port	= $_SERVER['SERVER_PORT'];
		$this->request_ssl	= (isset($_SERVER['HTTPS'])&&!empty($_SERVER['HTTPS']));
		$this->request_headers = apache_request_headers();
		$this->request_body	= file_get_contents('php://input');
		$this->api_call		= (isset($_GET['api']) && $_GET['api']==='api');
		$this->is_ajax 		= (isset($this->request_headers['X-Requested-With']) && $this->request_headers['X-Requested-With'] == 'XMLHttpRequest');
		
		$this->get 		= $_GET;
		$this->post		= $_POST;
		$this->files		= $_FILES;
		$this->request	= $_REQUEST;
		$this->server		= $_SERVER;
		$this->session	= &$_SESSION;
		
		foreach ($this->post as $key=>$value) {
			$this->post[$key] = filter_var($value,FILTER_SANITIZE_SPECIAL_CHARS);
		}
		
		// Format Files Array
		if (is_array($this->files) && !empty($this->files)) {
			$file = array();
			
			foreach ($this->files as $item => $value) {
				if (isset($value['name']) && is_array($value['name'])) {
					$file[$item] = self::format_files_array($value);
				} else {
					$file[$item][] = $value;
				}
			}
			
			$this->files = $file;
		}
		
	}
	
	/**
	 * Singleton design pattern implementation
	 * 
	 * @return Request
	 */
	public static function get_instance () {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Return the type of the current http request
	 * 
	 * @return string
	 */
	public static function get_request_type () {
		
		return self::get_instance()->request_type;
		
	}
	
	/**
	 * Return the port used by the user in the current request
	 * 
	 * @return string
	 */
	public static function get_request_port () {
		
		return self::get_instance()->request_port;
		
	}
	
	/**
	 * Return headers received from the current request
	 *
	 * @return string
	 */
	public static function get_request_headers () {
	
		return self::get_instance()->request_headers;
	
	}
	
	/**
	 * Return raw request body
	 * 
	 * @return string
	 */
	public static function get_request_body () {
		
		return self::get_instance()->request_body;
		
	}
	
	/**
	 * Checks if the request is made through the HTTPS protocol
	 * 
	 * @return boolean
	 */
	public static function is_https () {
		
		return self::get_instance()->request_ssl;
		
	}
	
	/**
	 * Checks if the request is made to the API
	 * 
	 * @return boolean
	 */
	public static function is_api_call () {
		
		$return = false;
		
		if (self::get_instance()->api_call == true) {
			$return = true;
		}
		
		return $return;
		
	}
	
	/**
	 * Checks if the request is made from a Javascript script
	 * 
	 * @return boolean
	 */
	public static function is_ajax () {
		
		return self::get_instance()->is_ajax;
		
	}
	
	/**
	 * Returns weither the current http request is a GET request or not
	 * 
	 * @return boolean
	 */
	public static function is_get () {
		
		$return=false;
		
		if (self::get_instance()->request_type == "GET") {
			$return=true;
		}
		
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a POST request or not
	 * 
	 * @return boolean
	 */
	public static function is_post () {
		
		$return = false;
		
		if (self::get_instance()->request_type == "POST") {
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a PUT request or not
	 * 
	 * @return boolean
	 */
	public static function is_put () {
		
		$return = false;
		
		if (self::get_instance()->request_type == "PUT") {
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * Returns weither the current http request is a DELETE request or not
	 * 
	 * @return boolean
	 */
	public static function is_delete () {
		
		$return = false;
		
		if (self::get_instance()->request_type == "DELETE") {
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * Return GET input
	 * 
	 * @return array
	 */
	public static function get () {
		
		return self::get_instance()->get;
		
	}
	
	/**
	 * Return POST input
	 * 
	 * @return array
	 */
	public static function post () {
		
		return self::get_instance()->post;
		
	}
	
	/**
	 * Return uploaded file input
	 * 
	 * @return array
	 */
	public static function files () {
		
		return self::get_instance()->files;
		
	}
	
	/**
	 * Return Request information
	 * 
	 * @return array
	 */
	public static function request () {
		
		return self::get_instance()->request;
		
	}
	
	/**
	 * Return server information
	 * 
	 * @return array
	 */
	public static function server () {
		
		return self::get_instance()->server;
		
	}
	
	/**
	 * Reformat the $_FILES array to something more handy
	 * 
	 * @param array $files
	 * @return array
	 */
	private static function format_files_array (Array $files) {
		
		$result = array();
		
		foreach ($files as $key1 => $value1) {
			foreach ($value1 as $key2 => $value2) {
				$result[$key2][$key1] = $value2;
			}
		}
		
		return $result;
		
	}
}
