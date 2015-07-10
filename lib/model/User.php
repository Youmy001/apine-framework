<?php
/**
 * This file contains the user class
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */
require_once ('lib/entity/AbstractEntity.php');
require_once ('lib/model/factory/user_factory.php');

/**
 * Implementation of the database representation of users
 */
class Apine_User extends AbstractEntity{

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

		$this->_initialize('users', $a_id);
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
		$this->_set_field('name', $a_name);
	
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
		$this->_set_field('pwd', $a_pass);
	
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
		$this->_set_field('Email', $a_email);
	
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
	 * (non-PHPdoc)
	 * @see EntityInterface::load()
	 */
	public function load(){

		if(!is_null($this->id)){
			$this->username = $this->_get_field('name');
			$this->password = $this->_get_field('pwd');
			$this->type = $this->_get_field('type');
			$this->email_address = $this->_get_field('Email');
			$this->register_date = $this->_get_field('register');
			$this->loaded = 1;
		}
	
	}

	/**
	 * (non-PHPdoc)
	 * @see EntityInterface::save()
	 */
	public function save(){

		parent::_save();
		$this->set_id($this->_get_id());
		
	}

	/**
	 * (non-PHPdoc)
	 * @see EntityInterface::delete()
	 */
	public function delete(){

	}

}
?>
