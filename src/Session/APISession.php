<?php
/**
 * This file contains the session management class for RESTful APIs
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Session;

use Apine;
use Apine\Application\Application;
use Apine\Core\Request;
use Apine\User\User;
use Apine\User\UserToken;
use Apine\User\Factory\UserFactory;

/**
 * Gestion and configuration of the a user session on a RESTful service
 * This class manages user login and logout
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
final class APISession implements SessionInterface
{
    /**
     * Session token for currently logged in user
     *
     * @var UserToken
     */
    private $token;
    
    /**
     * Is a user logged in or not
     *
     * @var boolean
     */
    private $logged_in = false;
    
    /**
     * Token duration
     *
     * @var integer
     */
    private $token_lifespan = 600;
    
    /**
     * Type of the current user
     *
     * @var integer
     */
    private $session_type = APINE_SESSION_GUEST;
    
    /**
     * Construct the session handler
     * Fetch data from request headers and authenticate the user
     */
    public function __construct()
    {
        $config = Application::getInstance()->getConfig();
        
        if (!is_null($config->session->token_lifespan)) {
            $this->token_lifespan = (int)$config->session->token_lifespan;
        }
        
        $request = Request::getInstance();
        
        if (isset($request->getRequestHeaders()['X-API-Authentication'])) {
            
            $authorization_string = $request->getRequestHeaders()['X-API-Authentication'];
            $authorization_array = explode(':', $authorization_string);
            $name = $authorization_array[0];
            $token = $authorization_array[1];
            $referer = isset($request->server()['REMOTE_ADDR']) ? $request->server()['REMOTE_ADDR'] : '';
            $agent = isset($request->server()['HTTP_USER_AGENT']) ? $request->server()['HTTP_USER_AGENT'] : '';
            
            $token_id = Apine\User\Factory\UserTokenFactory::authentication($name, $token, $this->token_lifespan);
            $token = Apine\User\Factory\UserTokenFactory::createById($token_id);
            
            if ($token_id && $token->getOrigin() == $referer . $agent) {
                $this->logged_in = true;
                $this->token = $token;
                $this->session_type = $this->token->getUser()->getType();
                
                $this->token->setLastAccessDate(date('Y-m-d H:i:s', time() + $this->token_lifespan));
                $this->token->save();
            }
            
        } else {
            if (isset($_COOKIE['apine_session'])) {
                
                $session = new WebSession();
                $data = $session->data();
                
                if ($data != null) {
                    $user_id = $data->get_var('apine_user_id');
                    
                    if ($user_id != null) {
                        $user = UserFactory::createById($user_id);
                        $token = new UserToken();
                        $token->set_user($user);
                        $this->logged_in = true;
                        $this->token = $token;
                        $this->session_type = $data->getVariable('apine_user_type');
                        $this->token->setLastAccessDate(date('Y-m-d H:i:s', time() + $this->token_lifespan));
                    }
                }
                
            }
        }
    }
    
    /**
     * Get the unique login token string
     *
     * @return string
     */
    public function getSessionIdentifier()
    {
        return ($this->isLoggedIn()) ? $this->token->getToken() : null;
    }
    
    /**
     * Get the login token
     *
     * @return UserToken
     */
    public function getToken()
    {
        return ($this->isLoggedIn()) ? $this->token : null;
    }
    
    /**
     * Verifies if a user is logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return (boolean)$this->logged_in;
    }
    
    /**
     * Get logged in user
     *
     * @return Apine\User\User
     */
    public function getUser()
    {
        if ($this->isLoggedIn()) {
            return $this->token->getUser();
        } else {
            return null;
        }
    }
    
    /**
     * Get logged in user's id
     *
     * @return integer
     */
    public function getUserId()
    {
        if ($this->isLoggedIn()) {
            return $this->token->getUser()->getId();
        } else {
            return null;
        }
    }
    
    /**
     * Get current session access level
     *
     * @return integer
     */
    public function getSessionType()
    {
        return $this->session_type;
    }
    
    /**
     * Set current session access level
     *
     * @param integer $a_type
     *        Session access level type
     *
     * @return integer
     */
    public function setSessionType($a_type)
    {
        $constants = get_defined_constants(true);
        $constants = $constants['user'];
        $type = false;
        
        foreach ($constants as $name => $value) {
            if (strstr($name, 'APINE_SESSION') && $value == $a_type) {
                $type = $a_type;
                $this->session_type = $a_type;
            }
        }
        
        return $type;
    }
    
    /**
     * Return current token duration
     *
     * @return integer
     */
    public function getSessionLifespan()
    {
        return $this->token_lifespan;
    }
    
    public function isSessionAdmin()
    {
        return ($this->session_type == APINE_SESSION_ADMIN) ? true : false;
    }
    
    public function isSessionNormal()
    {
        return ($this->session_type <= APINE_SESSION_USER) ? true : false;
    }
    
    public function isSessionGuest()
    {
        return ($this->session_type <= APINE_SESSION_GUEST) ? true : false;
    }
    
    /**
     * Log a user in
     * Look up in database for a matching row with a username and a
     * password
     *
     * @param string $a_user_name
     *        Username of the user
     * @param string $a_password
     *        Password of the user
     *
     * @return boolean
     */
    public function login($a_user_name, $a_password)
    {
        if (!$this->isLoggedIn()) {
            if ((Apine\User\Factory\UserFactory::isNameExist($a_user_name) || Apine\User\Factory\UserFactory::isEmailExist($a_user_name))) {
                $encode_pass = Apine\Core\Encryption::hashPassword($a_password);
            } else {
                return false;
            }
            
            $user_id = Apine\User\Factory\UserFactory::authentication($a_user_name, $encode_pass);
            $request_server = Apine\Core\Request::server();
            
            if ($user_id) {
                $referer = isset($request_server['REMOTE_ADDR']) ? $request_server['REMOTE_ADDR'] : '';
                $agent = isset($request_server['HTTP_USER_AGENT']) ? $request_server['HTTP_USER_AGENT'] : '';
                $creation_time = time();
                $new_user_token = new Apine\User\UserToken();
                $new_user_token->setUser($user_id);
                $new_user_token->setToken(Apine\Core\Encryption::hashUserToken($a_user_name, $a_password,
                    $creation_time));
                $new_user_token->setOrigin($referer . $agent);
                $new_user_token->setCreationDate($creation_time);
                $new_user_token->save();
                
                $this->token = $new_user_token;
                $this->setSessionType($this->token->getUser()->getType());
                $this->logged_in = true;
                
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function getExpirationTime()
    {
        return date('d M Y H:i:s', strtotime($this->token->getLastAccessDate()) + $this->token_lifespan);
    }
    
    /**
     * Log a user out
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->token->disable();
            $this->token->save();
            $return = true;
        } else {
            $return = false;
        }
        
        return $return;
    }
}