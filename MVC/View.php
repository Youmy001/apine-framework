<?php
/**
 * Views
 * This script contains views for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

use Apine\Core\Collection;
use Apine\Core\Request;

/**
 * Abstraction of the View part of the MVC pattern implementation
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @abstract
 */
abstract class View {
	
	/**
	 * Variables to be accessible by the view
	 * 
	 * @var Apine\Core\Collection
	 */
	protected $_params;
	
	/**
	 * List of HTTP headers to apply
	 * 
	 * @var array
	 */
	protected $_headers;
	
	/**
	 * Construct abstract View
	 */
	public function __construct() {
		
		$this->_params = new Collection();
		//$this->_params = array();
		$this->_headers = array();
		
	}
	
	public function __toString() {
		
		$this->draw();
		
	}
	
	/**
	 * Set a variable to be accessible by the view
	 * 
	 * @param string $a_name
	 * @param mixed $a_data
	 */
	final public function set_param($a_name,$a_data) {
		
		//$this->_params[$a_name] = $a_data;
		$this->_params->add_item($a_data, $a_name);
		
	}
	
	/**
	 * Set a header rule
	 * 
	 * @param string $a_rule
	 * @param string $a_name
	 */
	final public function set_header_rule($a_rule,$a_value=null) {
		
		$this->_headers[$a_rule] = $a_value;
		
	}
	
	/**
	 * Apply header rules
	 */
	final public function apply_headers() {
		
		if (count($this->_headers)>0) {
			foreach ($this->_headers as $key=>$value) {
				if ($value!=null) {
					header("$key: $value");
				} else {
					header("$key");
				}
			}
		}
		
	}
	
	/**
	 * Set HTTP Response Code Header 
	 * 
	 * @param integer $code
	 * @return integer
	 */
	final public function set_response_code($code) {
		
		if ($code !== NULL) {
			switch ($code) {
				case 100: $text = 'Continue'; break;
				case 101: $text = 'Switching Protocols'; break;
				case 200: $text = 'OK'; break;
				case 201: $text = 'Created'; break;
				case 202: $text = 'Accepted'; break;
				case 203: $text = 'Non-Authoritative Information'; break;
				case 204: $text = 'No Content'; break;
				case 205: $text = 'Reset Content'; break;
				case 206: $text = 'Partial Content'; break;
				case 300: $text = 'Multiple Choices'; break;
				case 301: $text = 'Moved Permanently'; break;
				case 302: $text = 'Moved Temporarily'; break;
				case 303: $text = 'See Other'; break;
				case 304: $text = 'Not Modified'; break;
				case 305: $text = 'Use Proxy'; break;
				case 400: $text = 'Bad Request'; break;
				case 401: $text = 'Unauthorized'; break;
				case 402: $text = 'Payment Required'; break;
				case 403: $text = 'Forbidden'; break;
				case 404: $text = 'Not Found'; break;
				case 405: $text = 'Method Not Allowed'; break;
				case 406: $text = 'Not Acceptable'; break;
				case 407: $text = 'Proxy Authentication Required'; break;
				case 408: $text = 'Request Time-out'; break;
				case 409: $text = 'Conflict'; break;
				case 410: $text = 'Gone'; break;
				case 411: $text = 'Length Required'; break;
				case 412: $text = 'Precondition Failed'; break;
				case 413: $text = 'Request Entity Too Large'; break;
				case 414: $text = 'Request-URI Too Large'; break;
				case 415: $text = 'Unsupported Media Type'; break;
				case 418: $text = 'I\'m a teapot'; break;
				case 500: $text = 'Internal Server Error'; break;
				case 501: $text = 'Not Implemented'; break;
				case 502: $text = 'Bad Gateway'; break;
				case 503: $text = 'Service Unavailable'; break;
				case 504: $text = 'Gateway Time-out'; break;
				case 505: $text = 'HTTP Version not supported'; break;
				default:
					exit('Unknown http status code "' . htmlentities($code) . '"');
					break;
			}
		
			$protocol = (isset(Request::server()['SERVER_PROTOCOL']) ? Request::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
			$this->set_header_rule($protocol . ' ' . $code . ' ' . $text);
			$GLOBALS['http_response_code'] = $code;
		} else {
			$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
		}
		
		return $code;
		
	}
	
	/**
	 * Send the view to output
	 */
	abstract public function draw();
	
	/**
	 * Return the content of the view
	 */
	abstract public function content();
	
}
