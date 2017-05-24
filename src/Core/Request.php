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
 * Handle information from the request and user inputs
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Request
{
    /**
     * Instance of the Request
     * Singleton Implementation
     *
     * @var Request
     */
    private static $instance;
    
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
     * @var string
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
    private $api_call = false;
    
    /**
     * Session AJAX Call
     *
     * @var boolean
     */
    private $is_ajax = false;
    
    /**
     * Headers received
     *
     * @var string[]
     */
    private $request_headers;
    
    private $request_resource;
    
    private $request_locale;
    
    /**
     * Construct the Request Management handler
     * Extract information from the request and clean user inputs
     */
    private function __construct()
    {
        
        $request_string = $_GET['apine-request'];
        $request_array = explode("/", $request_string);
        
        if ($request_array[0] === 'api') {
            $this->request_resource = substr($request_string, 3);
            $this->api_call = true;
        } else {
            $results = array();
            if (preg_match('([a-zA-Z]{2}(-[a-zA-Z]{2})?)', $request_array[0], $results)) {
                $this->request_locale = $results[0];
                $this->request_resource = substr($request_string, strlen($results[0]));
            } else {
                if ($request_string == '/') {
                    $this->request_resource = '/';
                } else {
                    $this->request_resource = $request_string;
                }
            }
        }
        
        unset($_GET['apine-request']);
        
        $this->request_type = $_SERVER['REQUEST_METHOD'];
        $this->request_port = $_SERVER['SERVER_PORT'];
        $this->request_ssl = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']));
        $this->request_headers = apache_request_headers();
        $this->request_body = file_get_contents('php://input');
        $this->is_ajax = (isset($this->request_headers['X-Requested-With']) && $this->request_headers['X-Requested-With'] == 'XMLHttpRequest');
        
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->request = $_REQUEST;
        $this->server = $_SERVER;
        $this->session = &$_SESSION;
        
        foreach ($this->post as $key => $value) {
            $this->post[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        // Format Files Array
        if (is_array($this->files) && !empty($this->files)) {
            $file = array();
            
            foreach ($this->files as $item => $value) {
                if (isset($value['name']) && is_array($value['name'])) {
                    $file[$item] = self::formatFilesArray($value);
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
    public static function getInstance()
    {
        
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     * Return the type of the current http request
     *
     * @return string
     */
    public static function getRequestType()
    {
        return self::getInstance()->request_type;
    }
    
    /**
     * Return the port used by the user in the current request
     *
     * @return string
     */
    public static function getRequestPort()
    {
        return self::getInstance()->request_port;
    }
    
    /**
     * Return headers received from the current request
     *
     * @return string
     */
    public static function getRequestHeaders()
    {
        return self::getInstance()->request_headers;
    }
    
    /**
     * Return raw request body
     *
     * @return string
     */
    public static function getRequestBody()
    {
        return self::getInstance()->request_body;
    }
    
    public static function getRequestResource()
    {
        
        return self::getInstance()->request_resource;
        
    }
    
    /**
     * Return an aggregate of all the input that are sent directly to the controllers through the routing procedure
     *
     * @return array
     */
    public static function getRequestParams()
    {
        $params = array();
        $gets = self::getInstance()->get();
        
        $params = array_merge($params, $gets);
        
        if (self::getInstance()->api_call) { /* RESTful Call */
            // first of all, pull the GET vars
            if (isset($_SERVER['QUERY_STRING'])) {
                mb_parse_str($_SERVER['QUERY_STRING'], $params);
            }
            
            $body = self::getInstance()->request_body;
            $content_type = false;
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $content_type = substr($_SERVER['CONTENT_TYPE'], 0, strpos($_SERVER['CONTENT_TYPE'], ';'));
            }
            switch ($content_type) {
                case "application/json":
                    $body_params = json_decode($body);
                    
                    if ($body_params) {
                        foreach ($body_params as $param_name => $param_value) {
                            $params[$param_name] = $param_value;
                        }
                    }
                    
                    break;
                case "application/x-www-form-urlencoded":
                    mb_parse_str($body, $postvars);
                    
                    foreach ($postvars as $field => $value) {
                        $params[$field] = urldecode($value);
                        
                    }
                    
                    break;
                default:
                    // we could parse other supported formats here
                    $params['request_body'] = $body;
                    break;
            }
        } else { /* Web App Call */
            // Add post arguments to args array
            if (self::getInstance()->request_type == "POST") {
                $params = array_merge($params, self::getInstance()->post);
            }
        }
        
        if (!empty(self::getInstance()->files)) {
            $params = array_merge($params, array("uploads" => self::getInstance()->files));
        }
        
        return $params;
    }
    
    /**
     * Checks if the request is made through the HTTPS protocol
     *
     * @return boolean
     */
    public static function isHttps()
    {
        return self::getInstance()->request_ssl;
    }
    
    /**
     * Checks if the request is made to the API
     *
     * @return boolean
     */
    public static function isApiCall()
    {
        $return = false;
        
        if (self::getInstance()->api_call == true) {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Checks if the request is made from a Javascript script
     *
     * @return boolean
     */
    public static function isAjax()
    {
        return self::getInstance()->is_ajax;
    }
    
    /**
     * Returns weither the current http request is a GET request or not
     *
     * @return boolean
     */
    public static function isGet()
    {
        $return = false;
        
        if (self::getInstance()->request_type == "GET") {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Returns weither the current http request is a POST request or not
     *
     * @return boolean
     */
    public static function isPost()
    {
        $return = false;
        
        if (self::getInstance()->request_type == "POST") {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Returns weither the current http request is a PUT request or not
     *
     * @return boolean
     */
    public static function isPut()
    {
        $return = false;
        
        if (self::getInstance()->request_type == "PUT") {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Returns weither the current http request is a DELETE request or not
     *
     * @return boolean
     */
    public static function isDelete()
    {
        $return = false;
        
        if (self::getInstance()->request_type == "DELETE") {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Return GET input
     *
     * @return array
     */
    public static function get()
    {
        return self::getInstance()->get;
    }
    
    /**
     * Return POST input
     *
     * @return array
     */
    public static function post()
    {
        return self::getInstance()->post;
    }
    
    /**
     * Return uploaded file input
     *
     * @return array
     */
    public static function files()
    {
        return self::getInstance()->files;
    }
    
    /**
     * Return Request information
     *
     * @return array
     */
    public static function request()
    {
        return self::getInstance()->request;
    }
    
    /**
     * Return server information
     *
     * @return array
     */
    public static function server()
    {
        return self::getInstance()->server;
    }
    
    /**
     * Reformat the $_FILES array to something more handy
     *
     * @param array $files
     *
     * @return array
     */
    private static function formatFilesArray(Array $files)
    {
        $result = array();
        
        foreach ($files as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $result[$key2][$key1] = $value2;
            }
        }
        
        return $result;
    }
}
