<?php
/**
 * This file contains the session management strategy
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Session;

use Apine;
use Apine\User\User;

/**
 * Abstraction for session management using the strategy pattern
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
final class SessionManager
{
    /**
     * Instance of the implementation
     *
     * @var SessionInterface
     */
    private $strategy;
    
    /**
     * Instance of the Session Manager
     * Singleton Implementation
     *
     * @var SessionManager
     */
    private static $instance;
    
    /**
     * Instantiation of the strategy
     */
    private function __construct()
    {
        if (Apine\Core\Request::isApiCall()) {
            $request = Apine\Core\Request::getInstance();
            
            if (isset($request->getRequestHeaders()['Authorization'])) {
                $this->strategy = new APISession();
            } else {
                if (isset($_COOKIE['apine_session'])) {
                    $this->strategy = new WebSession();
                } else {
                    $this->strategy = new APISession();
                }
            }
        } else {
            $this->strategy = new WebSession();
        }
    }
    
    /**
     * Singleton design pattern implementation
     *
     * @return SessionManager
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     * Fetch the unique identifier for the current session
     *
     * @return string
     */
    public static function getSessionIdentifier()
    {
        return self::getInstance()->strategy->getSessionIdentifier();
    }
    
    /**
     * Verifies if a user is logged in
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        return self::getInstance()->strategy->isLoggedOn();
    }
    
    /**
     * Get logged in user
     *
     * @return Apine\User\User
     */
    public static function getUser()
    {
        return self::getInstance()->strategy->getUser();
    }
    
    /**
     * Get logged in user's id
     *
     * @return integer
     */
    public static function getUserId()
    {
        return self::getInstance()->strategy->getUserId();
    }
    
    /**
     * Get current session access level
     *
     * @return integer
     */
    public static function getSessionType()
    {
        return self::getInstance()->strategy->getSessionType();
    }
    
    /**
     * Set current session access level
     *
     * @param integer $a_type
     *        Session access level type
     *
     * @return integer
     */
    public static function setSessionType($a_type)
    {
        return self::getInstance()->strategy->setSessionType($a_type);
    }
    
    /**
     * Return current session lifespan
     *
     * @return integer
     */
    public function getSessionLifespan()
    {
        return self::getInstance()->strategy->getSessionLifespan();
    }
    
    /**
     * Verify if the current session access level is admin
     *
     * @return boolean
     */
    public static function isSessionAdmin()
    {
        return self::getInstance()->strategy->isSessionAdmin();
    }
    
    /**
     * Verify if the current session access level is normal user
     *
     * @return boolean
     */
    public static function isSessionNormal()
    {
        return self::getInstance()->strategy->isSessionNormal();
    }
    
    /**
     * Verify if the current session access level is guest
     *
     * @return boolean
     */
    public static function isSessionGuest()
    {
        return self::getInstance()->strategy->isSessionGuest();
    }
    
    /**
     * Log a user in
     *
     * @param string $username
     *        Username of the user
     * @param string $password
     *        Password of the user
     *
     * @return boolean
     */
    public static function login($username, $password)
    {
        if (func_num_args() === 3) {
            $options = func_get_arg(2);
        } else {
            $options = array();
        }
        
        return self::getInstance()->strategy->login($username, $password, $options);
    }
    
    /**
     * Log a user out
     *
     * @return boolean
     */
    public static function logout()
    {
        return self::getInstance()->strategy->logout();
    }
    
    /**
     * Returns the session handler
     *
     * @return SessionInterface
     */
    public static function get_handler()
    {
        return self::getInstance()->strategy;
    }
    
}