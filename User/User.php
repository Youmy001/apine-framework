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
use Apine\Entity\OverloadEntityModel;
use Apine\Entity\EntityInterface;

/**
 * Implementation of the database representation of users
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 */
class User extends OverloadEntityModel {

	/**
	 * User identifier in database
	 * 
	 * @var integer
	 */
	protected $id;

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
	protected $group;

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

	protected $field_mapping = array(
		'register' => 'register_date',
		'email' => 'email_address'
	);

	/**
	 * User class' constructor
	 * 
	 * @param integer $a_id
	 *        User identifier
	 */
	public function __construct ($a_id = null) {

		$this->_initialize('apine_users', $a_id);
	
	}
	
	/**
	 * Fetch user's group
	 * 
	 * @return Collection
	 */
	final public function get_group () {
	
		if ($this->group == null) {
			$this->group= Factory\UserGroupFactory::create_by_user($this->get_id());
		}
		
		return $this->group;
	
	}
	
	/**
	 * Set user's group
	 * 
	 * @param Collection $a_group_list
	 *        List of User's groups
     * @throws \Exception
	 */
	final public function set_group ($a_group_list) {
		
		if (is_a($a_group_list, 'Apine\Core\Collection')) {
			$valid=true;
			
			foreach ($a_group_list as $item) {
				if (!is_a($item, 'Apine\User\UserGroup')) {
					$valid=false;
				}
			}
			
			if ($valid) {
				$this->group = $a_group_list;
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
	 * @param UserGroup $a_group
	 * @return boolean
	 */
	final public function has_group ($a_group) {
		
		if ($this->group == null) {
			$this->group=Factory\UserGroupFactory::create_by_user($this->get_id());
		}
		
		if (is_numeric($a_group)) {
			if (Factory\UserGroupFactory::is_id_exist($a_group)) {
				$is_group=$this->group->value_exists(new UserGroup($a_group));
			} else {
				$is_group=false;
			}
		} else if (is_a($a_group, 'Apine\User\UserGroup')) {
			$is_group=$this->group->value_exists($a_group);
		} else {
			$is_group=false;
		}
		
		return $is_group;
		
	}

	/**
	 * Set user's email address
	 * 
	 * @param string $a_email
	 *        User's email address
     * @throws \Exception
	 */
	final public function set_email_address ($a_email) {

		if (!filter_var($a_email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid Email Address');
		}

		parent::set_email_address($a_email);
	
	}

	/**
	 * Set user's registration date
	 * 
	 * @param string $a_timestamp
	 *        User's registration date
     * @throws \Exception
	 */
	final public function set_register_date ($a_timestamp) {
		
		if (is_string($a_timestamp) && strtotime($a_timestamp)) {
			$register_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
		} else if (is_long($a_timestamp) && date('u', $a_timestamp)) {
			$register_date = date('Y-m-d H:i:s', $a_timestamp);
		} else {
            throw new \Exception('Invalid UNIX Timestamp');
		}
		
		parent::set_register_date($register_date);
	
	}

    /**
     * Fetch a property
     *
     * @param string $a_name
     * @return mixed
     */
	public function get_property ($a_name) {
		
		if (is_null($this->properties)) {
			$this->load_properties();
		}
		
		return ($this->properties[$a_name]) ? $this->properties[$a_name]->get_value(): null;
		
	}

    /**
     * Fetch every properties
     *
     * @return array
     */
	public function get_property_all () {
		
		if (is_null($this->properties)) {
			$this->load_properties();
		}
		
		return $this->properties;
		
	}

    /**
     * Set a property
     *
     * @param string $a_name
     * @param mixed $a_value
     */
	public function set_property ($a_name, $a_value) {
		
		if (is_null($this->properties)) {
			$this->load_properties();
		}
		
		if (isset($this->properties[$a_name])) {
			$this->properties[$a_name]->set_value($a_value);
		} else {
			$property = new Property();
			$property->set_user($this->id);
			$property->set_name($a_name);
			$property->set_value($a_value);
			$this->properties[$a_name] = $property;
		}
		
	}

    /**
     * Remove a property
     *
     * @param string $a_name
     */
	public function unset_property ($a_name) {
		
		if (is_null($this->properties)) {
			$this->load_properties();
		}
		
		if (null !== $this->properties[$a_name]) {
			$this->properties[$a_name]->delete();
			unset($this->properties[$a_name]);
		}
		
	}

    /**
     * Load Properties
     */
	private function load_properties () {
		
		$database = new Apine\Core\Database();
		$request = $database->prepare('SELECT `id`, `name`, `value` FROM `apine_user_properties` WHERE `user_id` = ? ORDER BY `name` ASC');
		$data = $database->execute(array($this->id), $request);
		
		if ($data != null && count($data) > 0) {
			foreach ($data as $item) {
				$this->properties[$item['name']] = new Property($item['id']);
			}
		}
		
	}

	/**
	 * @see EntityInterface::save()
	 */
	public function save () {
		
		parent::save();
		
		if ($this->get_group()->length() > 0) { 
			$db = new Apine\Core\Database();
			$db->delete('apine_users_user_groups', array("user_id" => $this->get_id()));
		
			foreach ($this->get_group() as $item) {
				$db->insert('apine_users_user_groups', array("user_id" => $this->get_id(), "group_id" => $item->get_id()));
			}
		}
		
		if (count($this->properties) > 0) {
			foreach ($this->properties as $item) {
				$item->save();
			}
		}
		
	}

	/**
	 * @see EntityInterface::delete()
	 */
	public function delete () {
		
		$db = new Apine\Core\Database();
		$db->delete('apine_users_user_groups', array("user_id" => $this->get_id()));

		parent::delete();
		
	}

}
