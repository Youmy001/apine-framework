<?php
/**
 * This file contains the user class
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\User;

use Apine;

/**
 * Implementation of the database representation of users
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
class User extends Apine\Entity\EntityModel {

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

	/**
	 * User class' constructor
	 * 
	 * @param integer $a_id
	 *        User identifier
	 */
	public function __construct ($a_id = null) {

		$this->_initialize('apine_users', $a_id);
		
		if (!is_null($a_id)) {
			$this->id = $a_id;
		}
	
	}

	/**
	 * Fetch user's identifier
	 * 
	 * @return integer
	 */
	final public function get_id () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->id;
	
	}

	/**
	 * Set user's id
	 * 
	 * @param integer $a_id
	 *        User's identifier
	 */
	final public function set_id ($a_id) {

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('id', $a_id);
	
	}

	/**
	 * Fetch user's username
	 * 
	 * @return string
	 */
	final public function get_username () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->username;
	
	}

	/**
	 * Set user's username
	 * 
	 * @param string $a_name
	 *        User's username
	 */
	final public function set_username ($a_name) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->username = $a_name;
		$this->_set_field('username', $a_name);
	
	}

	/**
	 * Fetch user's encrypted password
	 * 
	 * @return string
	 */
	final public function get_password () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->password;
	
	}

	/**
	 * Set user's encrypted password
	 * 
	 * @param string $a_pass
	 *        User's password
	 */
	final public function set_password ($a_pass) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->password = $a_pass;
		$this->_set_field('password', $a_pass);
	
	}

	/**
	 * Fetch user's permission level
	 * 
	 * @return integer
	 */
	final public function get_type () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->type;
	
	}

	/**
	 * Set user's permission level
	 * 
	 * @param integer $a_type
	 *        User's permissions
	 */
	final public function set_type ($a_type) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->type = $a_type;
		$this->_set_field('type', $a_type);
	
	}
	
	/**
	 * Fetch user's group
	 * 
	 * @return ApineCollection
	 */
	final public function get_group () {
	
		if ($this->group == null) {
			$this->group= Factory\UserGroupFactory::create_by_user($this->id);
		}
		
		return $this->group;
	
	}
	
	/**
	 * Set user's group
	 * 
	 * @param ApineCollection $a_group_list
	 *        List of User's groups
	 */
	final public function set_group ($a_group_list) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
	
		if (is_a($a_group_list, 'Apine\Core\Collection')) {
			$valid=true;
			
			foreach ($a_group_list as $item) {
				if (!is_a($item, 'Apine\User\UserGroup')) {
					$valid=false;
				}
			}
			
			if ($valid) {
				$this->group = $a_group_list;
 			}
		} else {
			return null;
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
			$this->group=Factory\UserGroupFactory::create_by_user($this->id);
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
	 * Fetch user's email address
	 * 
	 * @return string
	 */
	final public function get_email_address () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->email_address;
	
	}

	/**
	 * Set user's email address
	 * 
	 * @param string $a_email
	 *        User's email address
	 */
	final public function set_email_address ($a_email) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (filter_var($a_email, FILTER_VALIDATE_EMAIL)) {
			$this->email_address = $a_email;
			$this->_set_field('email', $a_email);
		} else {
			return false;
		}
	
	}

	/**
	 * Fetch user's registration date
	 * 
	 * @return string
	 */
	final public function get_register_date () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->register_date;
	
	}

	/**
	 * Set user's registration date
	 * 
	 * @param string $a_timestamp
	 *        User's registration date
	 */
	final public function set_register_date ($a_timestamp) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (is_string($a_timestamp) && strtotime($a_timestamp)) {
			$this->register_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
		} else if (is_long($a_timestamp) && date('u', $a_timestamp)) {
			$this->register_date = date('Y-m-d H:i:s', $a_timestamp);
		} else {
			return false;
		}
		
		$this->_set_field('register', $this->register_date);
		return $this->register_date;
	
	}
	
	public function get_property ($a_name) {
		
		if (is_null($this->properties)) {
			$this->load_properties();
		}
		
		return ($this->properties[$a_name]) ? $this->properties[$a_name]->get_value(): null;
		
	}
	
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
	
	public function unset_property ($a_name) {
		
		if (is_null($this->properties)) {
			$this->load_properties();
		}
		
		if (null !== $this->properties[$a_name]) {
			$this->properties[$a_name]->delete();
			unset($this->properties[$a_name]);
		}
		
	}
	
	private function load_properties () {
		
		$database = new Apine\Core\Database();
		$request = $database->prepare('SELECT `id`, `name` FROM `apine_user_properties` WHERE `user_id` = ? ORDER BY `name` ASC');
		$data = $database->execute(array($this->id), $request);
		
		if ($data != null && count($data) > 0) {
			foreach ($data as $item) {
				$this->properties[$item['name']] = new Property($this->id);
			}
		}
		
	}
	
	/**
	 * @see ApineEntityInterface::load()
	 */
	public function load () {

		if (!is_null($this->id)) {
			$this->username = $this->_get_field('username');
			$this->password = $this->_get_field('password');
			$this->type = $this->_get_field('type');
			$this->email_address = $this->_get_field('email');
			$this->register_date = $this->_get_field('register');
			$this->loaded = 1;
		}
	
	}

	/**
	 * @see ApineEntityInterface::save()
	 */
	public function save () {
		
		parent::_save();
		$this->set_id($this->_get_id());
		
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
	 * @see ApineEntityInterface::delete()
	 */
	public function delete () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		$db = new Apine\Core\Database();
		$db->delete('apine_users_user_groups', array("user_id" => $this->get_id()));

		parent::_destroy();
		
	}

}
