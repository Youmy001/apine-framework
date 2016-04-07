<?php
/**
 * Internal URL Writer
 * This script contains an helper to write internal URL
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

use Apine\Core\Request as Request;
use Apine\Session\SessionManager as SessionManager;
use Apine\Application\ApplicationTranslator as ApplicationTranslator;
use Apine\Application\Application as Application;

/**
 * Internal URL Writer
 * Write URL from server's informations
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class URLHelper {
	
	/**
	 * Instance of the URL Writer
	 * Singleton Implementation
	 *
	 * @var ApineURLHelper
	 */
	private static $_instance;
	
	/**
	 * Server Domain Name
	 * 
	 * @var string
	 */
	private $server_app_root;
	
	/**
	 * Main Server's Domain Name
	 * 
	 * @var string
	 */
	private $server_main_root;
	
	/**
	 * Path of the current session
	 * 
	 * @var string
	 */
	private $session_path;
	
	/**
	 * Construct the URL Writer helper
	 * 
	 * Extract string from server configuration
	 */
	private function __construct() {
		
		// Set server address
		$this->server_app_root = Request::server()['SERVER_NAME'];
		$ar_domain = explode('.', Request::server()['SERVER_NAME']);
		
		if (count($ar_domain) >= 3) {
			$start = strlen($ar_domain[0]) + 1;
			$this->server_main_root = substr(Request::server()['SERVER_NAME'], $start);
		} else {
			$this->server_main_root = Request::server()['SERVER_NAME'];
		}
		
		if ((!Request::is_https() && Request::get_request_port() != 80) || (Request::is_https() && Request::get_request_port() != 443)) {
			$this->server_app_root .= ":" . Request::get_request_port();
			$this->server_main_root .= ":" . Request::get_request_port();
		}
		
		if (isset(Request::request()['request'])) {
			$ar_path = explode('/', Request::request()['request']);
			array_shift($ar_path);
			$this->session_path = implode('/', $ar_path);
		} else {
			$this->session_path = '';
		}
		
		$webroot = Application::get_instance()->get_webroot();
		if (!is_null($webroot) && !empty($webroot)) {
			$this->server_app_root .= "/" . $webroot;
			$this->server_main_root .= "/" . $webroot;
		}
		
	}
	
	/**
	 * Singleton design pattern implementation
	 *
	 * @static
	 * @return ApineURLHelper
	 */
	public static function get_instance() {
		
		if (!isset(self::$_instance)) {
			self::$_instance = new static();
		}
		
		return self::$_instance;
		
	}
	
	/**
	 * Select protocol to use
	 * 
	 * @param integer $param
	 * @return string
	 */
	private static function protocol ($param) {
		
		if (!apine_application()->get_use_https()) {
			$protocol = 'http://';
		} else if (apine_application()->get_secure_session() && SessionManager::is_logged_in()) {
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
					$protocol = (Request::is_https()) ? 'https://' : 'http://';
					break;
			}
		}
		
		return $protocol;
	}
	
	/**
	 * Append a path to the current absolute path
	 * 
	 * @param string $base
	 * 			Base url
	 * @param string $path
	 *        String to append
	 * @param integer $protocol
	 *        Protocol to append to the path
	 * @return string
	 */
	private static function write_url ($base, $path, $protocol) {
	
		if (isset(Request::get()['language'])) {
			if (Request::get()['language'] == ApplicationTranslator::language()->code || Request::get()['language'] == ApplicationTranslator::language()->code_short) {
				$language = Request::get()['language'];
			} else {
				$language = ApplicationTranslator::language()->code_short;
			}
			return self::protocol($protocol) . $base . '/' . $language . '/' . $path;
		} else {
			return self::protocol($protocol) . $base . '/' . $path;
		}
	
	}
	
	public static function resource ($path) {
		
		return self::protocol(APINE_PROTOCOL_DEFAULT) . self::get_instance()->server_app_root . '/' . $path;
		
	}
	
	/**
	 * Retrieve the http path to a ressource relative to site's root
	 * 
	 * @param string $path
	 *        String to append
	 * @param integer $protocol
	 *        Protocol to append to the path
	 * @return string
	 */
	public static function path($path, $protocol = APINE_PROTOCOL_DEFAULT) {
		
		return self::write_url(self::get_instance()->server_app_root, $path, $protocol);
		
	}
	
	/**
	 * Retrieve the http path to a ressource relative to site's main
	 * domains's root
	 * 
	 * @param string $path
	 *        String to append
	 * @param integer $protocol
	 *        Protocol to append to the path
	 * @return string
	 */
	public static function main_path($path, $protocol = APINE_PROTOCOL_DEFAULT) {
		
		return self::write_url(self::get_instance()->server_main_root, $path, $protocol);
	
	}
	
	/**
	 * Retrieve the http path to a ressource relative to current
	 * ressource
	 * 
	 * @param string $path
	 *        String to append
	 * @param integer $protocol
	 *        Protocol to append to the path
	 * @return string
	 */
	public static function relative_path($path, $protocol = APINE_PROTOCOL_DEFAULT) {
	
		return self::write_url(self::get_instance()->server_app_root, self::get_instance()->session_path . '/' . $path, $protocol);
	
	}
	
	/**
	 * Get current current http path
	 * 
	 * @return string
	 */
	public static function get_current_path($protocol = APINE_PROTOCOL_DEFAULT) {
	
		return self::write_url(self::get_instance()->server_app_root, self::get_instance()->session_path, $protocol);
	
	}
}
