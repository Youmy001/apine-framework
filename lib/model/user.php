<?php
/**
 * This file contains the user class
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */

/**
 * Implementation of the database representation of users
 */
class ApineUser extends ApineEntityModel {

	/**
	 * User identifier in database
	 * @var integer
	 */
	protected $id;

	/**
	 * Username
	 * @var string
	 */
	protected $username;

	/**
	 * User encrypted password
	 * @var string
	 */
	protected $password;

	/**
	 * User permissions
	 * @var integer
	 */
	protected $type;
	
	/**
	 * User Group
	 * @var ApineCollection[ApineUserGroup]
	 */
	protected $group;

	/**
	 * User email address
	 * @var string
	 */
	protected $email_address;

	/**
	 * Registration date's timestamp
	 * @var string
	 */
	protected $register_date;

	/**
	 * User class' constructor
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
	 * @return integer
	 */
	public function get_id () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->id;
	
	}

	/**
	 * Set user's id
	 * @param integer $a_id
	 *        User's identifier
	 */
	public function set_id ($a_id) {

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('id', $a_id);
	
	}

	/**
	 * Fetch user's username
	 * @return string
	 */
	public function get_username () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->username;
	
	}

	/**
	 * Set user's username
	 * @param string $a_name
	 *        User's username
	 */
	public function set_username ($a_name) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->username = $a_name;
		$this->_set_field('username', $a_name);
	
	}

	/**
	 * Fetch user's encrypted password
	 * @return string
	 */
	public function get_password () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->password;
	
	}

	/**
	 * Set user's encrypted password
	 * @param string $a_pass
	 *        User's password
	 */
	public function set_password ($a_pass) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->password = $a_pass;
		$this->_set_field('password', $a_pass);
	
	}

	/**
	 * Fetch user's permission level
	 * @return integer
	 */
	public function get_type () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->type;
	
	}

	/**
	 * Set user's permission level
	 * @param integer $a_type
	 *        User's permissions
	 */
	public function set_type ($a_type) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->type = $a_type;
		$this->_set_field('type', $a_type);
	
	}
	
	/**
	 * Fetch user's group
	 * @return ApineUserGroup
	 */
	public function get_group () {
	
		if ($this->group == null) {
			$this->group=ApineUserGroupFactory::create_by_user($this->id);
		}
		
		return $this->group;
	
	}
	
	/**
	 * Set user's group
	 * @param <ApineCollection[ApineUserGroup]> $a_group_list
	 *        List of User's groups
	 */
	public function set_group ($a_group_list) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
	
		if (get_class($a_group_list) == 'ApineCollection') {
			$valid=true;
			
			foreach ($a_group_list as $item) {
				if (!get_class($item)=='ApineUserGroup') {
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
	 * @param <ApineUserGroup, int> $a_group
	 * @return boolean
	 */
	public function has_group ($a_group) {
		
		if ($this->group == null) {
			$this->group=ApineUserGroupFactory::create_by_user($this->id);
		}
		
		if (is_numeric($a_group) && ApineUserGroupFactory::is_id_exist($a_group)) {
			$is_group=$this->group->value_exists(new ApineUserGroup($a_group));
		} else if (get_class($a_group) == 'ApineUserGroup') {
			$is_group=$this->group->value_exists($a_group);
		} else {
			$is_group=false;
		}
		
		return $is_group;
		
	}

	/**
	 * Fetch user's email address
	 * @return string
	 */
	public function get_email_address () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->email_address;
	
	}

	/**
	 * Set user's email address
	 * @param string $a_email
	 *        User's email address
	 */
	public function set_email_address ($a_email) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->email_address = $a_email;
		$this->_set_field('email', $a_email);
	
	}

	/**
	 * Fetch user's registration date
	 * @return string
	 */
	public function get_register_date () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->register_date;
	
	}

	/**
	 * Set user's registration date
	 * @param string $a_timestamp
	 *        User's registration date
	 */
	public function set_register_date ($a_timestamp) {

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
		
		$db = new ApineDatabase();
		$db->delete('apine_users_user_groups', array("user_id" => $this->get_id()));
		
		foreach ($this->get_group() as $item) {
			$db->insert('apine_users_user_groups', array("user_id" => $this->get_id(), "group_id" => $item->get_id()));
		}
		
	}

	/**
	 * @see ApineEntityInterface::delete()
	 */
	public function delete () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		$db = new ApineDatabase();
		$db->delete('apine_users_user_groups', array("user_id" => $this->get_id()));

		parent::_destroy();
		
	}

}
?>
