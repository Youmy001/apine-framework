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
use Apine\Application\Translator as Translator;
use Apine\Application\Application as Application;

/**
 * Internal URL Writer
 * Write URL from server's informations
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
final class URLHelper
{
    /**
     * Instance of the URL Writer
     * Singleton Implementation
     *
     * @var URLHelper
     */
    private static $instance;
    
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
     * Extract string from server configuration
     */
    private function __construct()
    {
        // Set server address
        $this->server_app_root = Request::server()['SERVER_NAME'];
        $ar_domain = explode('.', Request::server()['SERVER_NAME']);
        
        if (count($ar_domain) >= 3) {
            $start = strlen($ar_domain[0]) + 1;
            $this->server_main_root = substr(Request::server()['SERVER_NAME'], $start);
        } else {
            $this->server_main_root = Request::server()['SERVER_NAME'];
        }
        
        if ((!Request::isHttps() && Request::getRequestPort() != 80) || (Request::isHttps() && Request::getRequestPort() != 443)) {
            $this->server_app_root .= ":" . Request::getRequestPort();
            $this->server_main_root .= ":" . Request::getRequestPort();
        }
        
        if (isset(Request::request()['request'])) {
            $ar_path = explode('/', Request::request()['request']);
            array_shift($ar_path);
            $this->session_path = implode('/', $ar_path);
        } else {
            $this->session_path = '';
        }
        
        $webroot = Application::getInstance()->getWebroot();
        if (!is_null($webroot) && !empty($webroot)) {
            $this->server_app_root .= "/" . $webroot;
            $this->server_main_root .= "/" . $webroot;
        }
    }
    
    /**
     * Singleton design pattern implementation
     *
     * @static
     * @return URLHelper
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     * Select protocol to use
     *
     * @param integer $param
     *
     * @return string
     */
    private static function protocol($param)
    {
        /*$application = Application::get_instance();
        
        if (!$application->get_use_https()) {
            $protocol = 'http://';
        } else if ($application->get_secure_session() && SessionManager::is_logged_in()) {
            $protocol = 'https://';
        } else {*/
        switch ($param) {
            case 0:
                $protocol = 'http://';
                break;
            case 1:
            case 2:
            default:
                $protocol = 'https://';
                break;
        }
        
        //}
        
        return $protocol;
    }
    
    /**
     * Append a path to the current absolute path
     *
     * @param string  $base
     *            Base url
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    private static function writeUrl($base, $path, $protocol)
    {
        if (isset(Request::get()['language'])) {
            if (Request::get()['language'] == Translator::language()->code || Request::get()['language'] == Translator::language()->code_short) {
                $language = Request::get()['language'];
            } else {
                $language = Translator::language()->code_short;
            }
            
            return self::protocol($protocol) . $base . '/' . $language . '/' . $path;
        } else {
            return self::protocol($protocol) . $base . '/' . $path;
        }
    }
    
    public static function resource($path)
    {
        return self::protocol(APINE_PROTOCOL_DEFAULT) . self::getInstance()->server_app_root . '/' . $path;
    }
    
    /**
     * Retrieve the http path to a ressource relative to site's root
     *
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    public static function path($path, $protocol = APINE_PROTOCOL_DEFAULT)
    {
        return self::writeUrl(self::getInstance()->server_app_root, $path, $protocol);
    }
    
    /**
     * Retrieve the http path to a ressource relative to site's main
     * domains's root
     *
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    public static function mainPath($path, $protocol = APINE_PROTOCOL_DEFAULT)
    {
        return self::writeUrl(self::getInstance()->server_main_root, $path, $protocol);
    }
    
    /**
     * Retrieve the http path to a ressource relative to current
     * ressource
     *
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    public static function relativePath($path, $protocol = APINE_PROTOCOL_DEFAULT)
    {
        return self::writeUrl(self::getInstance()->server_app_root, self::getInstance()->session_path . '/' . $path,
            $protocol);
    }
    
    /**
     * Get current current http path
     *
     * @param integer $protocol
     *
     * @return string
     */
    public static function getCurrentPath($protocol = APINE_PROTOCOL_DEFAULT)
    {
        return self::writeUrl(self::getInstance()->server_app_root, self::getInstance()->session_path, $protocol);
    }
}
