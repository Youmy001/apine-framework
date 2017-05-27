<?php
/**
 * This file contains the user property class
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */

namespace Apine\User;

use Apine\Entity\EntityModel;

/**
 * Implementation of the database representation of user properties
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 * @method string getName() Get the name of the property
 * @method setName(string $a_name) Set the name of the property
 */
final class Property extends EntityModel
{
    /**
     * @var User
     */
    protected $user;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var mixed
     */
    protected $value;
    
    /**
     * Property constructor.
     *
     * @param integer $a_id
     */
    public function __construct($a_id = null)
    {
        $this->initialize('apine_user_properties', $a_id);
    }
    
    /**
     * Get the owner of the property
     *
     * @return User
     */
    public function getUser()
    {
        if (is_null($this->user)) {
            $this->user = Factory\UserFactory::createById($this->get('user_id'));
        }
        
        return $this->user;
    }
    
    /**
     * Set the owner of the property
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
     * Fetch the value of the property
     *
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($this->value)) {
            $value = $this->get('value');
    
            if (@unserialize($value) !== false) {
                $this->value = @unserialize($value);
            } else {
                if ($value === serialize(false)) {
                    $this->value = false;
                } else {
                    $this->value = $value;
                }
            }
        }
        
        return $this->value;
    }
    
    /**
     * Set the value of the property
     *
     * @param mixed $a_value
     */
    public function setValue($a_value)
    {
        if (null !== $value = serialize($a_value)) {
            $this->value = $a_value;
            
            if (!is_null($a_value)) {
                $this->set('value', serialize($a_value));
            } else {
                $this->set('value', null);
            }
        }
    }
    
    public function serialize($data = false)
    {
        if ($data) {
            return $this->getValue();
        } else {
            return parent::serialize();
        }
    }
}