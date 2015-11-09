<?php
/**
 * Internal URL Writer
 * This script contains an helper to write internal URL
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

define('APINE_PROTOCOL_HTTP', 0);
define('APINE_PROTOCOL_HTTPS', 1);
define('APINE_PROTOCOL_DEFAULT', 2);

/**
 * Internal URL Writer
 * Write URL from server's informations
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class ApineURLHelper {
	
	private static $_instance;
	
	/**
	 * Server Domain Name
	 * @var string
	 */
	private $session_server;
	
	/**
	 * Path on the server
	 * @var string
	 */
	private $session_server_path;
	
	/**
	 * Script's path on the server
	 * @var string
	 */
	private $session_current_path;
	
	/**
	 * Script's name
	 * @var string
	 */
	private $session_current;
	
	/**
	 * Main Server's Domain Name
	 * @var string
	 */
	private $main_session_server;
	
	/**
	 * Construct the URL Writer helper
	 * Extract string from server configuration
	 */
	private function __construct() {
		
		// Set server address
		//$protocol = (ApineRequest::is_https())?'https://':'http://';
		//$this->session_server = $protocol . ApineRequest::server()['SERVER_NAME'];
		$this->session_server = ApineRequest::server()['SERVER_NAME'];
		$ar_domain = explode('.', ApineRequest::server()['SERVER_NAME']);
		
		if (count($ar_domain) >= 3) {
			$start = strlen($ar_domain[0]) + 1;
			//$this->main_session_server = $protocol . substr(ApineRequest::server()['SERVER_NAME'], $start);
			$this->main_session_server = substr(ApineRequest::server()['SERVER_NAME'], $start);
		} else {
			//$this->main_session_server = $protocol . ApineRequest::server()['SERVER_NAME'];
			$this->main_session_server = ApineRequest::server()['SERVER_NAME'];
		}
		
		if ((!ApineRequest::is_https() && ApineRequest::get_request_port() != 80) || (ApineRequest::is_https() && ApineRequest::get_request_port() != 443)) {
			$this->session_server .= ":" . ApineRequest::get_request_port();
			$this->main_session_server .= ":" . ApineRequest::get_request_port();
		}
		
		// Set script name
		//$this->session_current = $protocol . ApineRequest::server()['SERVER_NAME'] . ApineRequest::server()['PHP_SELF'];
		$this->session_current = ApineRequest::server()['SERVER_NAME'] . ApineRequest::server()['PHP_SELF'];
		// Set script path
		//$this->session_current_path = $protocol . ApineRequest::server()['SERVER_NAME'] . dirname(ApineRequest::server()['PHP_SELF']);
		$this->session_current_path = ApineRequest::server()['SERVER_NAME'] . dirname(ApineRequest::server()['PHP_SELF']);
		// Set server path
		$this->session_server_path = realpath(dirname(dirname(__FILE__)) . '/..');
		
	}
	
	/**
	 * Singleton design pattern implementation
	 *
	 * @static
	 * @return URL_Helper
	 */
	public static function get_instance() {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	private static function protocol ($param) {
		
		if (ApineConfig::get('runtime', 'secure_session') === 'yes' && ApineSession::is_logged_in()) {
			$protocol = 'https://';
		} else {
			switch ($param) {
				case 0:
					$protocol = 'http://';
					break;
				case 1:
					$protocol = 'https://';
					break;
				case 2:
				default:
					$protocol = (ApineRequest::is_https())?'https://':'http://';
					break;
			}
		}
		
		return $protocol;
	}
	
	/**
	 * Append a path to the current absolute path
	 * 
	 * @param string $base
	 * @param string $path
	 *        String to append
	 * @return string
	 */
	private static function write_url ($base, $path, $protocol) {
	
		if (isset(ApineRequest::get()['language'])) {
			if (ApineRequest::get()['language'] == ApineAppTranslator::language()->code || ApineRequest::get()['language'] == ApineAppTranslator::language()->code_short) {
				$language = ApineRequest::get()['language'];
			} else {
				$language = ApineAppTranslator::language()->code_short;
			}
			return self::protocol($protocol) . $base . '/' . $language . '/' . $path;
		} else {
			return self::protocol($protocol) . $base . '/' . $path;
		}
	
	}
	
	public static function resource ($path) {
		
		return self::protocol(APINE_PROTOCOL_DEFAULT) . self::get_instance()->session_server . '/' . $path;
		
	}
	
	/**
	 * Retrieve the http path to a ressource relative to site's root
	 * 
	 * @param string $path
	 *        String to append
	 * @param boolean $add_arg
	 *        Whether to add language argument to path
	 * @return string
	 */
	public static function path($path, $protocol = APINE_PROTOCOL_DEFAULT) {
		
		return self::write_url(self::get_instance()->session_server, $path, $protocol);
		
	}
	
	/**
	 * Retrieve the http path to a ressource relative to site's main
	 * domains's root
	 * 
	 * @param string $path
	 *        String to append
	 * @param boolean $add_arg
	 *        Whether to add language argument to path
	 * @return string
	 */
	public static function main_path($path, $protocol = APINE_PROTOCOL_DEFAULT) {
		
		return self::write_url(self::get_instance()->main_session_server, $path, $protocol);
	
	}
	
	/**
	 * Retrieve the http path to a ressource relative to current
	 * ressource
	 * 
	 * @param string $path
	 *        String to append
	 * @param string $add_arg
	 *        Whether to add language argument to path
	 * @return string
	 */
	public static function relative_path($path, $protocol = APINE_PROTOCOL_DEFAULT) {
	
		return self::write_url(self::get_instance()->session_current_path, $path, $protocol);
	
	}
	
	/**
	 * Get current absolute path
	 * 
	 * @return string
	 */
	public static function get_current_path() {
	
		return self::get_instance()->session_current;
	
	}
	
	/**
	 * Get current absolute server path
	 * 
	 * @return string
	 */
	public static function get_server_path() {
	
		return self::get_instance()->session_server_path;
	
	}
}

class_alias('ApineURLHelper','URL_Helper');