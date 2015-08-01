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
class ApineUser extends ApineEntityModel{

	/**
	 * User identifier in database
	 * @var integer
	 */
	private $id;

	/**
	 * Username
	 * @var string
	 */
	private $username;

	/**
	 * User encrypted password
	 * @var string
	 */
	private $password;

	/**
	 * User permissions
	 * @var integer
	 */
	private $type;
	
	/**
	 * User Group
	 * @var ApineUserGroup
	 */
	private $group;

	/**
	 * User email address
	 * @var string
	 */
	private $email_address;

	/**
	 * Registration date's timestamp
	 * @var string
	 */
	private $register_date;

	/**
	 * User class' constructor
	 * @param integer $a_id
	 *        User identifier
	 */
	public function __construct($a_id = null){

		$this->_initialize('apine_users', $a_id);
		if(!is_null($a_id)){
			$this->id = $a_id;
		}
	
	}

	/**
	 * Fetch user's identifier
	 * @return integer
	 */
	public function get_id(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->id;
	
	}

	/**
	 * Set user's id
	 * @param integer $a_id
	 *        User's identifier
	 */
	public function set_id($a_id){

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('ID', $a_id);
	
	}

	/**
	 * Fetch user's username
	 * @return string
	 */
	public function get_username(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->username;
	
	}

	/**
	 * Set user's username
	 * @param string $a_name
	 *        User's username
	 */
	public function set_username($a_name){

		if($this->loaded == 0){
			$this->load();
		}
		$this->username = $a_name;
		$this->_set_field('username', $a_name);
	
	}

	/**
	 * Fetch user's encrypted password
	 * @return string
	 */
	public function get_password(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->password;
	
	}

	/**
	 * Set user's encrypted password
	 * @param string $a_pass
	 *        User's password
	 */
	public function set_password($a_pass){

		if($this->loaded == 0){
			$this->load();
		}
		$this->password = $a_pass;
		$this->_set_field('password', $a_pass);
	
	}

	/**
	 * Fetch user's permission level
	 * @return integer
	 */
	public function get_type(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->type;
	
	}

	/**
	 * Set user's permission level
	 * @param integer $a_type
	 *        User's permissions
	 */
	public function set_type($a_type){

		if($this->loaded == 0){
			$this->load();
		}
		$this->type = $a_type;
		$this->_set_field('type', $a_type);
	
	}
	
	/**
	 * Fetch user's group
	 * @return Group
	 */
	public function get_group(){
	
		if($this->loaded == 0){
			$this->load();
		}
		return $this->group;
	
	}
	
	/**
	 * Set user's group
	 * @param <Group, integer> $a_type
	 *        User's group
	 */
	public function set_group($a_type){
	
		if($this->loaded == 0){
			$this->load();
		}
		if(is_numeric($a_type)){
			if(GroupFactory::is_id_exist($a_type)){
				$this->group = GroupFactory::create_by_id($a_type);
			}
		}else if(get_class($a_type) == 'Group'){
			$this->group = $a_type;
		}
		$this->_set_field('group', $this->type->get_id());
	
	}

	/**
	 * Fetch user's email address
	 * @return string
	 */
	public function get_email_address(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->email_address;
	
	}

	/**
	 * Set user's email address
	 * @param string $a_email
	 *        User's email address
	 */
	public function set_email_address($a_email){

		if($this->loaded == 0){
			$this->load();
		}
		$this->email_address = $a_email;
		$this->_set_field('email', $a_email);
	
	}

	/**
	 * Fetch user's registration date
	 * @return string
	 */
	public function get_register_date(){

		if($this->loaded == 0){
			$this->load();
		}
		return date(APP_DATE_FORMAT, strtotime($this->register_date));
	
	}

	/**
	 * Set user's registration date
	 * @param string $a_timestamp
	 *        User's registration date
	 */
	public function set_register_date($a_timestamp){

		if($this->loaded == 0){
			$this->load();
		}
		$this->register_date = $a_timestamp;
		$this->_set_field('register', $a_timestamp);
	
	}
	
	/**
	 * @see ApineEntityInterface::load()
	 */
	public function load(){

		if(!is_null($this->id)){
			$this->username = $this->_get_field('username');
			$this->password = $this->_get_field('password');
			$this->type = $this->_get_field('type');
			$this->group = ApineUserGroupFactory::create_by_id($this->_get_field('group'));
			$this->email_address = $this->_get_field('email');
			$this->register_date = $this->_get_field('register');
			$this->loaded = 1;
		}
	
	}

	/**
	 * @see ApineEntityInterface::save()
	 */
	public function save(){

		parent::_save();
		$this->set_id($this->_get_id());
		
	}

	/**
	 * @see ApineEntityInterface::delete()
	 */
	public function delete(){

	}

}
?>
