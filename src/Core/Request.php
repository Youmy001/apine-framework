<?php
/**
 * Request Management
 * This script contains an helper to handle request information
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Core;

use Apine\Core\Http\ServerRequest;

/**
 * Request Management Tool
 * Handle information from the request and user inputs
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Request extends ServerRequest
{
    
    /**
     * Instance of the Request
     * Singleton Implementation
     *
     * @var Request
     */
    //private static $instance;
    
    /**
     * @var string
     */
    private $requestAction;
    
    /**
     * @var integer
     */
    private $requestType;
    
    /**
     * Construct the Request Management handler
     * Extract information from the request and clean user inputs
     */
    public function __construct()
    {
        $gets = $_GET;
        $posts = $_POST;
        $cookies = $_COOKIE;
        $files = $_FILES;
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = getallheaders();
        $uri = self::getUriFromGlobals();
        $body = file_get_contents('php://input');
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        
        $request_string = $_GET['apine-request'];
        $request_array = explode("/", $request_string);
        
        if ($request_array[1] === 'api') {
            $this->requestAction = substr($request_string, 3);
            $this->requestType = APINE_REQUEST_MACHINE;
        } else {
            $this->requestType = APINE_REQUEST_USER;
    
            if ($request_string == '/') {
                $this->requestAction = '/';
            } else {
                $this->requestAction = $request_string;
            }
        }
    
        unset($gets['apine-request']);
        
        parent::__construct($method, $uri, $headers, $body, $protocol, $_SERVER);
        
        $request = $this
            ->withCookieParams($cookies)
            ->withQueryParams($gets)
            ->withParsedBody($posts)
            ->withUploadedFiles(self::formatFiles($files));
        
        return $request;
    }
    
    /**
     * Return the port used by the user in the current request
     *
     * @return integer
     */
    public function getPort() : integer
    {
        return $this->getUri()->getPort();
    }
    
    public function getAction() : string
    {
        return $this->requestAction;
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
    public function isHttps() : bool
    {
        $headers = $this->getServerParams();
        return (isset($headers['HTTPS']) && !empty($headers['HTTPS']));
    }
    
    /**
     * Checks if the request is made to the API
     *
     * @return boolean
     */
    public function isApiCall() : bool
    {
        return ($this->requestType == APINE_REQUEST_MACHINE);
    }
    
    /**
     * Checks if the request is made from a Javascript script
     *
     * @return boolean
     */
    public function isAjax() : bool
    {
        $headers = $this->getServerParams();
        return (isset($headers['X-Requested-With']) && $headers['X-Requested-With'] == 'XMLHttpRequest');
    }
    
    /**
     * Returns whether the current http request is a GET request or not
     *
     * @return boolean
     */
    public function isGet() : bool
    {
        return ($this->getMethod() == "GET");
    }
    
    /**
     * Returns whether the current http request is a POST request or not
     *
     * @return boolean
     */
    public function isPost() : bool
    {
        return ($this->getMethod() == "POST");
    }
    
    /**
     * Returns whether the current http request is a PUT request or not
     *
     * @return boolean
     */
    public function isPut() : bool
    {
        return ($this->getMethod() == "PUT");
    }
    
    /**
     * Returns whether the current http request is a DELETE request or not
     *
     * @return boolean
     */
    public function isDelete()
    {
        return ($this->getMethod() == "DELETE");
    }
    
}
