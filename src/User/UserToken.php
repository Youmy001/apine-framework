<?php
/**
 * API Login Token
 * This script contains the model representation of login tokens for
 * the RESTful API
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\User;

use Apine;
use Apine\Entity\EntityModel;

/**
 * Implementation of the database representation of api login tokens
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 * @method string getToken() Fetch the value of the token string
 * @method string getOrigin() Fetch the user origin string
 * @method string getCreationDate() Fetch the date of creation of the token
 * @method string getLastAccessDate() Fetch the date of the last access to the application using the token
 * @method boolean getDisabled() Fetch the value of the flag indicating if the token is disabled
 * @method setOrigin(string $string) Set the user origin string
 * @method setDisabled(boolean $boolean) Set the value of the flag indicating if the token is disabled
 */
class UserToken extends EntityModel
{
    /**
     * Token user
     *
     * @var Apine\User\User
     */
    protected $user;
    
    /**
     * Token string
     *
     * @var string
     */
    protected $token;
    
    /**
     * Token user origin
     *
     * @var string
     */
    protected $origin;
    
    /**
     * Token creation date
     *
     * @var string
     */
    protected $creation_date;
    
    /**
     * Token last access date
     *
     * @var string
     */
    protected $last_access_date;
    
    /**
     * Is token disabled
     *
     * @var boolean
     */
    protected $disabled = false;
    
    /**
     * ApineUserToken class' constructor
     *
     * @param integer $a_id
     *        Token identifier
     */
    public function __construct($a_id = null)
    {
        $this->initialize('apine_api_users_tokens', $a_id);
    }
    
    /**
     * Set token string
     *
     * @param string $a_token
     */
    public function setToken($a_token)
    {
        if (strlen($a_token) == 64) {
            parent::setToken($a_token);
        }
    }
    
    /**
     * Set token's creation date
     *
     * @param string $a_timestamp
     *        Token's creation date
     *
     * @throws \Exception If the the UNIX Timestamp is invalid
     */
    public function setCreationDate($a_timestamp)
    {
        if (is_string($a_timestamp) && strtotime($a_timestamp)) {
            $creation_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
        } else {
            if (is_long($a_timestamp) && date('u', $a_timestamp)) {
                $creation_date = date('Y-m-d H:i:s', $a_timestamp);
            } else {
                throw new \Exception('Invalid UNIX Timestamp');
            }
        }
        
        parent::setCreationDate($creation_date);
    }
    
    /**
     * Set token's last access date
     *
     * @param string $a_timestamp
     *        Token's creation date
     *
     * @throws \Exception If the the UNIX Timestamp is invalid
     */
    public function setLastAccessDate($a_timestamp)
    {
        if (is_string($a_timestamp) && strtotime($a_timestamp)) {
            $last_access_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
        } else {
            if (is_long($a_timestamp) && date('u', $a_timestamp)) {
                $last_access_date = date('Y-m-d H:i:s', $a_timestamp);
            } else {
                throw new \Exception('Invalid UNIX Timestamp');
            }
        }
        
        parent::setLastAccessDate($last_access_date);
    }
    
    /**
     * Fetch the token user
     *
     * @return Apine\User\User
     */
    public function getUser()
    {
        if (is_null($this->user)) {
            $this->user = Factory\UserFactory::createById($this->get('user_id'));
        }
        
        return $this->user;
    }
    
    /**
     * Set the token user
     *
     * @param User|integer $a_user
     *
     * @throws \Exception If the input value is invalid
     */
    public function setUser($a_user)
    {
        if (is_numeric($a_user) && Factory\UserFactory::isIdExist($a_user)) {
            $this->user = Factory\UserFactory::createById($a_user);
        } else {
            if (is_a($a_user, 'Apine\User\User')) {
                $this->user = $a_user;
            } else {
                throw new \Exception('Invalid User');
            }
        }
        
        $this->set('user_id', $this->user->getId());
    }
    
    /**
     * Disable a token
     */
    public function disable()
    {
        if (is_null($this->disabled)) {
            $this->disabled = (bool)$this->get('disabled');
        }
        
        parent::setDisabled(true);
    }
    
    /**
     * @see EntityInterface::save()
     */
    public function save()
    {
        if ($this->getCreationDate() == null) {
            $this->creation_date = date('Y-m-d H:i:s', time());
            $this->set('creation_date', $this->creation_date);
        }
        
        if ($this->getLastAccessDate() == null) {
            $this->last_access_date = date('Y-m-d H:i:s', time());
            $this->set('last_access_date', $this->last_access_date);
        }
        
        if ($this->getToken() === null || $this->getUser() === null) {
            throw new \Exception('Missing values', 500);
        }
        
        parent::save();
    }
}