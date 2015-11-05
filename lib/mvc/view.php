<?php
/**
 * Views
 * This script contains views for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Abstraction of the View part of the MVC pattern implementation
 * 
 * @author Tommy Teasdale
 * @abstract
 */
abstract class ApineView {
	
	/**
	 * Variables to be accessible by the view
	 * @var ApineCollection
	 */
	protected $_params;
	
	/**
	 * List of HTTP headers to apply
	 * @var ApineCollection
	 */
	protected $_headers;
	
	/**
	 * Construct abstract View
	 */
	public function __construct() {
		
		$this->_params=new ApineCollection();
		$this->_headers=new ApineCollection();
		
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
		
		$this->_params->add_item($a_data,$a_name);
		
	}
	
	/**
	 * Set a header rule
	 * 
	 * @param string $a_rule
	 * @param string $a_name
	 */
	final public function set_header_rule($a_rule,$a_name=null) {
		
		$this->_headers->add_item($a_name,$a_rule);
		
	}
	
	/**
	 * Apply header rules
	 */
	final public function apply_headers() {
		
		if ($this->_headers->length()>0) {
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
		
			$protocol = (isset(ApineRequest::server()['SERVER_PROTOCOL']) ? ApineRequest::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
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

/**
 * HTML View
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class ApineHTMLView extends ApineView {
	
	/**
	 * Path to layout file
	 * 
	 * @var string
	 */
	private $_layout;
	
	/**
	 * Path to view file
	 * @var string
	 */
	private $_view;
	
	/**
	 * Page Title
	 * @var string
	 */
	private $_title;
	
	/**
	 * List of scripts to include
	 * @var ApineCollection
	 */
	private $_scripts;
	
	/**
	 * View's HTML Document
	 * @var string $content
	 */
	private $content;
	
	/**
	 * Construct the HTML view
	 * 
	 * @param string $a_title
	 * @param string $a_view
	 * @param string $a_layout
	 */
	public function __construct($a_title = "", $a_view = "default", $a_layout = "default") {
		
		parent::__construct();
		$this->_scripts=new ApineCollection();
		
		$this->_title=$a_title;
		$this->set_view($a_view);
		
		if ($a_layout == "default") {
			$a_layout = ApineConfig::get('application', 'default_layout');
		}
		$this->set_layout($a_layout);
		
	}
	
	/**
	 * Set page title
	 * 
	 * @param string $a_title
	 */
	public function set_title($a_title) {
		
		if ($a_title!="") {
			$this->_title=$a_title;
		}
		
	}
	
	/**
	 * Set path to layout file
	 * 
	 * @param string $a_layout
	 */
	public function set_layout($a_layout) {
		
		if ($a_layout!="") {
			// Verify if the layout file exists
			if (file_exists("views/layouts/$a_layout.php")) {
				$this->_layout=$a_layout;
			} else {
				$this->_layout='default';
			}
		}
		
	}
	
	/**
	 * Set path to view file
	 * 
	 * @param string $a_view
	 */
	public function set_view($a_view) {
		
		if ($a_view!="") {
			// Verify if the view file exists
			if (file_exists("views/$a_view.php")) {
				$this->_view=$a_view;
			} else {
				$this->_view='default';
			}
		}
		
	}
	
	/**
	 * Append javascript script to view
	 * 
	 * @param string $a_script URL to script
	 */
	public function add_script($a_script) {
		
		if ($a_script!="") {
			if (file_exists("resources/public/js/$a_script.js")) {
				$this->_scripts->add_item(ApineURLHelper::resource("resources/public/js/$a_script.js"));
			}
		}
		
	}
	
	/**
	 * Insert script into the view
	 */
	public function apply_script() {
		
		if ($this->_scripts->length()>0) {
			foreach ($this->_scripts as $value) {
				print("<script src=\"$value\"></script>");
			}
		}
		
	}
	
	/**
	 * Send the view to output
	 */
	public function draw() {
		
		$this->apply_headers();
		
		if (is_null($this->content)) {
			$this->content();
		}
		
		print $this->content;
		
	}
	
	/**
	 * Return the content of the view
	 */
	public function content() {
		
		ob_start();
		include_once("views/layouts/$this->_layout.php");
		$content = ob_get_contents();
		ob_end_clean();
		//die($content);
		$this->content = $content;
		
		return $content;
		
	}
}

/**
 * File View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class ApineFileView extends ApineView {
	
	/**
	 * View File
	 * 
	 * @var ApineFile
	 */
	private $_file;
	
	/**
	 * Construct File View 
	 * 
	 * @param ApineFile $a_file
	 */
	public function __construct(ApineFile $a_file=null) {
		
		parent::__construct();
		
		$this->set_file($a_file);
		
	}
	
	/**
	 * Set file
	 * 
	 * @param string|ApineFile $a_file
	 */
	public function set_file($a_file=null) {
		
		if (!$a_file==null) {
			if (is_string($a_file)) {
				$this->_file = new ApineFile($a_file);
			} else if (is_a($a_file,'ApineFile')) {
				$this->_file = $a_file;
			}
		}
		
	}
	
	/**
	 * Send View to output
	 */
	public function draw() {
		
		if (!$this->_file==null) {
			// Set headers
			// PHP must return an image instead of a html
			header("Content-type: ".$this->_file->type());
			// Tell the browser the image size
			header("Content-Length: " . $this->_file->size());
			
			$this->_file->output();
		}
		
	}
	
	/**
	 * Return the content of the view
	 */
	public function content () {
		
		ob_start();
		$this->_file->output();
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
}

/**
 * JSON View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class ApineJSONView extends ApineView {
	
	/**
	 * Json File
	 * 
	 * @var string
	 */
	private $_json_file;
	
	/**
	 * Set Json File
	 * 
	 * @param string|array $a_json
	 * @return string
	 */
	public function set_json_file($a_json) {
		
		if (is_string($a_json)) {
			// Verify if valid json array
			$result = json_decode($a_json);
			
			if (json_last_error() === JSON_ERROR_NONE) {
				$this->_json_file=$a_json;
				$return=$a_json;
			}else{
				$return=null;
			}
		} else if (is_object($a_json)) {
			$this->_json_file=json_encode($a_json);
			$return=$this->_json_file;
		} else if (is_array($a_json)) {
			$this->_json_file=json_encode($a_json);
			$return=$this->_json_file;
		} else {
			$return=null;
		}
		
		return $return;
		
	}
	
	/**
	 * Send View to output
	 */
	public function draw() {
		
		header('Content-type: application/json');
		$this->apply_headers();
		
		if($this->_json_file===null){
			// Encode Objects to Array
			
			//print_r($this->_params->get_all());
			// Encode to JSON
			$this->set_json_file($this->_params->get_all());
		}
		
		print $this->_json_file;
		
	}
	
	/**
	 * Return the content of the view
	 */
	public function content () {
		
		if (is_null($this->_json_file)) {
			$this->set_json_file($this->_params->get_all());
		}
		
		return $this->_json_file;
		
	}
	
}

class_alias('ApineView', 'View');
class_alias('ApineHTMLView', 'HMTLView');
class_alias('ApineFileView', 'FileView');
class_alias('ApineJSONView', 'JSONView');