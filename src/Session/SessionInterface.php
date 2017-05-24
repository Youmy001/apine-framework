<?php
/**
 * This file contains the interface for sesssion managers
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */

namespace Apine\Session;

/**
 * Interface SessionInterface
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
interface SessionInterface
{
    /**
     * @return boolean
     */
    public function isLoggedIn();
    
    /**
     * @return \Apine\User\User
     */
    public function getUser();
    
    /**
     * @return integer
     */
    public function getUserId();
    
    /**
     * @return string
     */
    public function getSessionIdentifier();
    
    /**
     * @return integer
     */
    public function getSessionType();
    
    /**
     * @param integer $a_type
     */
    public function setSessionType($a_type);
    
    /**
     * @return boolean
     */
    public function isSessionAdmin();
    
    /**
     * @return boolean
     */
    public function isSessionNormal();
    
    /**
     * @return boolean
     */
    public function isSessionGuest();
    
    /**
     * @param string $a_username
     * @param string $a_password
     *
     * @return boolean
     */
    public function login($a_username, $a_password);
    
    /**
     * @return boolean
     */
    public function logout();
    
}