<?php

abstract class View{
	
	protected $_params;
	
	public function __construct(){
		$this->_params=new Liste();
	}
	
	public function __toString(){
		$this->draw();
	}
	
	public function set_param($a_name,$a_data){
		$this->_params->add_item($a_data,$a_name);
	}
	
	public function set_header_rule($a_rule,$a_name=null){
		$this->_headers->add_item($a_name,$a_rule);
	}
	
	public function apply_headers(){
		if($this->_headers->length()>0){
			foreach ($this->_headers as $key=>$value){
				if($value!=null){
					header("$key: $value");
				}else{
					header("$key");
				}
			}
		}
	}
	
	public function set_response_code($code){
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
		
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		
			$this->set_header_rule($protocol . ' ' . $code . ' ' . $text);
		
			$GLOBALS['http_response_code'] = $code;
		
		} else {
		
			$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
		
		}
		
		return $code;
	}
	
	abstract function draw();
}

class HTTPView extends View{
	
	protected $_headers;
	
	public function __construct(){
		parent::__construct();
		
		$this->_headers=new Liste();
	}
	
	public function draw(){
		$this->apply_headers();
	}
	
}

class HTMLView extends HTTPView{
	
	private $_layout;
	
	private $_view;
	
	private $_title;
	
	private $_scripts;
	
	public function __construct($a_title="",$a_view="default",$a_layout="default"){
		parent::__construct();
		$this->_scripts=new Liste();
		
		$this->_title=$a_title;
		
		$this->set_layout($a_layout);
		$this->set_view($a_view);
	}
	
	public function set_title($a_title){
		if($a_title!=""){
			$this->_title=$a_title;
		}
	}
	
	public function set_layout($a_layout){
		if($a_layout!=""){
			// Verify if the layout file exists
			if(file_exists("views/layouts/$a_layout.php")){
				$this->_layout=$a_layout;
			}else{
				$this->_layout='default';
			}
		}
	}
	
	public function set_view($a_view){
		if($a_view!=""){
			// Verify if the view file exists
			if(file_exists("views/$a_view.php")){
				$this->_view=$a_view;
			}else{
				$this->_view='default';
			}
		}
	}
	
	public function add_script($a_script){
		if($a_script!=""){
			if(file_exists("resources/public/js/$a_script.js")){
				$this->_scripts->add_item(session()->path("resources/public/js/$a_script.js",false));
			}
		}
	}
	
	public function apply_script(){
		if($this->_scripts->length()>0){
			foreach ($this->_scripts as $value){
				print("<script src=\"$value\"></script>");
			}
		}
	}
	
	public function draw(){
		//global $session;
		
		$this->apply_headers();
		include_once("views/layouts/$this->_layout.php");
	}
}

class FileView extends HTTPView{
	
	private $_file;
	
	public function __construct($a_file=null){
		parent::__construct();
		
		$this->set_file($a_file);
		
	}
	
	public function set_file($a_file=null){
		if(!$a_file==null){
			if(is_string($a_file)){
				$this->_file = new File(SCRIPT_PATH . $a_file);
			}else if(is_a($a_file,'File')){
				$this->_file = $a_file;
			}
		}
	}
	
	public function draw(){
		if(!$this->_file==null){
			// Set headers
			// PHP must return an image instead of a html
			header("Content-type: ".$this->_file->get_type());
			// Tell the browser the image size
			header("Content-Length: " . $this->_file->get_size());
			
			$this->_file->read();
		}
	}
}

class JSONView extends View{
	
	public function draw(){
		header('Content-type: application/json');
		
		// Encode Objects to Array
		
		// Encode to JSON
	}
}