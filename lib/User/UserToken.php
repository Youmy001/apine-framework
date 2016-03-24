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

/**
 * Implementation of the database representation of api login tokens
 * 
 * @author Tommy Teasdale
 */
class UserToken extends Apine\Entity\EntityModel {

	/**
	 * Database identifier
	 * 
	 * @var integer
	 */
	private $id;

	/**
	 * Token user
	 * 
	 * @var ApineUser
	 */
	private $user;

	/**
	 * Token string
	 * 
	 * @var string
	 */
	private $token;

	/**
	 * Token user origin
	 * 
	 * @var string
	 */
	private $origin;

	/**
	 * Token creation date
	 * 
	 * @var string
	 */
	private $creation_date;

	/**
	 * Token last access date
	 * 
	 * @var string
	 */
	private $last_access_date;
	
	/**
	 * Is token disabled
	 * 
	 * @var boolean
	 */
	private $disabled = false;

	/**
	 * ApineUserToken class' constructor
	 * 
	 * @param integer $a_id
	 *        Token identifier
	 */
	public function __construct ($a_id = null) {

		$this->_initialize('apine_api_users_tokens', $a_id);
		
		if (!is_null($a_id)) {
			$this->id = $a_id;
		}
	
	}

	/**
	 * Fetch token's identifier
	 * 
	 * @return integer
	 */
	public function get_id () {

		if (!$this->_is_loaded()) {
			$this->load();
		}
		return $this->id;
	
	}

	/**
	 * Set token's id
	 * 
	 * @param integer $a_id
	 *        Token's identifier
	 */
	public function set_id ($a_id) {

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('id', $a_id);
		
		return $a_id;
	
	}

	/**
	 * Fetch token string
	 * 
	 * @return string
	 */
	public function get_token () {

		if (!$this->_is_loaded()) {
			$this->load();
		}
		
		return $this->token;
	
	}

	/**
	 * Set token string
	 * 
	 * @param string $a_token        
	 * @return string
	 */
	public function set_token ($a_token) {

		if (!$this->_is_loaded()) {
			$this->load();
		}
		
		if (strlen($a_token) == 64) {
			$this->token = $a_token;
			$this->_set_field('token', $a_token);
		} else {
			return false;
		}
		
		return $this->token;
	
	}

	/**
	 * Fetch origin string
	 * 
	 * @return string
	 */
	public function get_origin () {

		if (!$this->_is_loaded()) {
			$this->load();
		}
		
		return $this->origin;
	
	}

	/**
	 * Set origin string
	 * 
	 * @param string $a_origin
	 * @return string
	 */
	public function set_origin ($a_origin) {

		if (!$this->_is_loaded()) {
			$this->load();
		}
		
		$this->origin = $a_origin;
		$this->_set_field('origin', $a_origin);
		
		return $this->origin;
	
	}

	/**
	 * Fetch token's creation date
	 * 
	 * @return string
	 */
	public function get_creation_date () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->creation_date;
	
	}

	/**
	 * Set token's creation date
	 * 
	 * @param string $a_timestamp
	 *        Token's creation date
	 * @return string
	 */
	public function set_creation_date ($a_timestamp) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (is_string($a_timestamp) && strtotime($a_timestamp)) {
			$this->creation_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
		} else if (is_long($a_timestamp) && date('u', $a_timestamp)) {
			$this->creation_date = date('Y-m-d H:i:s', $a_timestamp);
		} else {
			return false;
		}
		
		$this->_set_field('creation_date', $this->creation_date);
		return $this->creation_date;
	
	}

	/**
	 * Fetch token's creation date
	 * 
	 * @return string
	 */
	public function get_last_access_date () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->creation_date;
	
	}

	/**
	 * Set token's creation date
	 * 
	 * @param string $a_timestamp
	 *        Token's creation date
	 * @return string
	 */
	public function set_last_access_date ($a_timestamp) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (is_string($a_timestamp) && strtotime($a_timestamp)) {
			$this->last_access_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
		} else if (is_long($a_timestamp) && date('u', $a_timestamp)) {
			$this->last_access_date = date('Y-m-d H:i:s', $a_timestamp);
		} else {
			return false;
		}
		
		$this->_set_field('last_access_date', $this->last_access_date);
		return $this->last_access_date;
	
	}
	
	/**
	 * Fetch the token user
	 * 
	 * @return ApineUser
	 */
	public function get_user () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->user;
		
	}
	
	/**
	 * Set the token user
	 * 
	 * @param <ApineUser|integer> $a_user
	 * @return <ApineUser|integer>
	 */
	public function set_user ($a_user) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (is_numeric($a_user) && Factory\UserFactory::is_id_exist($a_user)) {
			$this->user = Factory\UserFactory::create_by_id($a_user);
		} else if (is_a($a_user, 'Apine\User\User')) {
			$this->user = $a_user;
		} else {
			return false;
		}
		
		$this->_set_field('user_id', $this->user->get_id());
		return $this->user;
		
	}
	
	/**
	 * Disable a token
	 */
	public function disable () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->disabled = true;
		$this->_set_field('disabled', true);
		
	}

	/**
	 *
	 * @see ApineEntityInterface::load()
	 */
	public function load () {

		if (!is_null($this->id)) {
			$this->user = Factory\UserFactory::create_by_id($this->_get_field('user_id'));
			$this->token = $this->_get_field('token');
			$this->origin = $this->_get_field('origin');
			$this->creation_date = $this->_get_field('creation_date');
			$this->last_access_date = $this->_get_field('last_access_date');
			$this->disabled = (bool) $this->_get_field('disabled');
			$this->loaded = 1;
		}
	
	}

	/**
	 *
	 * @see ApineEntityInterface::save()
	 */
	public function save () {

		if (is_null($this->creation_date)) {
			$this->creation_date = date('Y-m-d H:i:s', time());
			$this->_set_field('creation_date', $this->creation_date);
		}
		
		if (is_null($this->last_access_date)) {
			$this->last_access_date = date('Y-m-d H:i:s', time());
			$this->_set_field('last_access_date', $this->last_access_date);
		}
		
		if (is_null($this->token) || is_null($this->user)) {
			throw new Apine\Exception\GenericException('Missing values', 500);
		}
		
		parent::_save();
		$this->set_id($this->_get_id());
	
	}

	/**
	 *
	 * @see ApineEntityInterface::delete()
	 */
	public function delete () {

		parent::_destroy();
	
	}

}