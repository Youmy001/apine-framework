<?php
/**
 * This file contains the user class
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\User;

use Apine;
use Apine\Core\Collection;
use Apine\Entity\EntityInterface;
use Apine\Entity\EntityModel;

/**
 * Implementation of the database representation of users
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 * @method string getUsername() Fetch the username of the user
 * @method string getPassword() Fetch the password of the user
 * @method string getEmailAddress() Fetch the email address of the user
 * @method string getRegisterDate() Fetch the date of registration of the user
 * @method integer getType() Fetch the type of the user
 * @method setUsername(string $string) Set the username of the user
 * @method setPassword(string $string) Set the password of the user
 * @method setType(integer $type) Set the type of the user
 */
class User extends EntityModel
{
    /**
     * Username
     *
     * @var string
     */
    protected $username;
    
    /**
     * User encrypted password
     *
     * @var string
     */
    protected $password;
    
    /**
     * User permissions
     *
     * @var integer
     */
    protected $type;
    
    /**
     * User Group
     *
     * @var Apine\Core\Collection[UserGroup]
     */
    protected $groups;
    
    /**
     * User email address
     *
     * @var string
     */
    protected $email_address;
    
    /**
     * Registration date's timestamp
     *
     * @var string
     */
    protected $register_date;
    
    /**
     * Custom User Properties
     *
     * @var array[Property]
     */
    protected $properties;
    
    /**
     * @see EntityModel::$field_mapping
     */
    protected $field_mapping = array(
        'register' => 'register_date',
        'email'    => 'email_address'
    );
    
    /**
     * User class' constructor
     *
     * @param integer $a_id
     *        User identifier
     */
    public function __construct($a_id = null)
    {
        $this->initialize('apine_users', $a_id);
    }
    
    /**
     * Fetch user's group
     *
     * @return Collection
     */
    final public function getGroup()
    {
        if ($this->groups == null) {
            if ($this->getId() !== null) {
                $this->groups = Factory\UserGroupFactory::createByUser($this->getId());
            } else {
                $this->groups = new Collection();
            }
        }
        
        return $this->groups;
    }
    
    /**
     * Set user's group
     *
     * @param Collection $a_group_list
     *        List of User's groups
     *
     * @throws \Exception
     */
    final public function setGroup($a_group_list)
    {
        if (is_a($a_group_list, 'Apine\Core\Collection')) {
            $valid = true;
            
            foreach ($a_group_list as $item) {
                if (!is_a($item, 'Apine\User\UserGroup')) {
                    $valid = false;
                }
            }
            
            if ($valid) {
                $this->groups = $a_group_list;
            } else {
                throw new \Exception('Invalid Group List');
            }
        } else {
            throw new \Exception('Invalid Group List');
        }
    }
    
    /**
     * Check if the user is member of a User Group
     *
     * @param int|UserGroup $a_group
     *
     * @return boolean
     */
    final public function hasGroup($a_group)
    {
        if ($this->groups == null) {
            if ($this->get_id() !== null) {
                $this->groups = Factory\UserGroupFactory::createByUser($this->get_id());
            } else {
                $this->groups = new Collection();
            }
        }
        
        $is_group = false;
        
        if (is_numeric($a_group)) {
            if (Factory\UserGroupFactory::isIdExist($a_group)) {
                foreach ($this->groups->getAll() as $key => $item) {
                    if (((int)$item->getId()) === $a_group) {
                        $is_group = true;
                        break;
                    }
                }
            }
        } else {
            if (is_a($a_group, 'Apine\User\UserGroup')) {
                foreach ($this->groups->getAll() as $key => $item) {
                    if (((int)$item->getId()) === ((int)$a_group->getId())) {
                        $is_group = true;
                        break;
                    }
                }
            }
        }
        
        return $is_group;
    }
    
    /**
     * Set user's email address
     *
     * @param string $a_email
     *        User's email address
     *
     * @throws \Exception
     */
    final public function setEmailAddress($a_email)
    {
        if (!filter_var($a_email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid Email Address');
        }
        
        parent::setEmailAddress($a_email);
    }
    
    /**
     * Set user's registration date
     *
     * @param string $a_timestamp
     *        User's registration date
     *
     * @throws \Exception If the the UNIX Timestamp is invalid
     */
    final public function setRegisterDate($a_timestamp)
    {
        if (is_string($a_timestamp) && strtotime($a_timestamp)) {
            $register_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
        } else {
            if (is_long($a_timestamp) && date('u', $a_timestamp)) {
                $register_date = date('Y-m-d H:i:s', $a_timestamp);
            } else {
                throw new \Exception('Invalid UNIX Timestamp');
            }
        }
        
        parent::setRegisterDate($register_date);
    }
    
    /**
     * Fetch a property
     *
     * @param string $a_name
     *
     * @return mixed
     */
    public function getProperty($a_name)
    {
        if (is_null($this->properties)) {
            $this->load_properties();
        }
        
        return (array_key_exists($a_name, $this->properties)) ? $this->properties[$a_name]->getValue() : null;
    }
    
    /**
     * Fetch every properties
     *
     * @return array
     */
    public function getPropertyAll()
    {
        if (is_null($this->properties)) {
            $this->load_properties();
        }
        
        return $this->properties;
    }
    
    /**
     * Set a property
     *
     * @param string $a_name
     * @param mixed  $a_value
     */
    public function setProperty($a_name, $a_value)
    {
        if (is_null($this->properties)) {
            $this->load_properties();
        }
        
        if (isset($this->properties[$a_name])) {
            $this->properties[$a_name]->setValue($a_value);
        } else {
            $property = new Property();
            $property->setName($a_name);
            $property->setValue($a_value);
            $this->properties[$a_name] = $property;
        }
    }
    
    /**
     * Remove a property
     *
     * @param string $a_name
     */
    public function unsetProperty($a_name)
    {
        if (is_null($this->properties)) {
            $this->loadProperties();
        }
        
        if (null !== $this->properties[$a_name]) {
            $this->properties[$a_name]->delete();
            unset($this->properties[$a_name]);
        }
    }
    
    /**
     * Load Properties
     */
    private function loadProperties()
    {
        $database = new Apine\Core\Database();
        $request = $database->prepare('SELECT `id`, `name`, `value` FROM `apine_user_properties` WHERE `user_id` = ? ORDER BY `name` ASC');
        $data = $database->execute(array($this->getId()), $request);
        
        if ($data != null && count($data) > 0) {
            foreach ($data as $item) {
                $this->properties[$item['name']] = new Property($item['id']);
            }
        }
    }
    
    /**
     * @see EntityInterface::save()
     */
    public function save()
    {
        parent::save();
        
        if ($this->getGroup()->length() > 0) {
            $db = new Apine\Core\Database();
            $db->delete('apine_users_user_groups', array("user_id" => $this->getId()));
            
            foreach ($this->getGroup() as $item) {
                $db->insert('apine_users_user_groups',
                    array("user_id" => $this->getId(), "group_id" => $item->getId()));
            }
        }
        
        if (count($this->properties) > 0) {
            foreach ($this->properties as $item) {
                if ($item->getUser() === null) {
                    $item->setUser($this->getId());
                }
                
                $item->save();
            }
        }
    }
    
    /**
     * @see EntityInterface::delete()
     */
    public function delete()
    {
        $db = new Apine\Core\Database();
        $db->delete('apine_users_user_groups', array("user_id" => $this->getId()));
        
        parent::delete();
    }
}
