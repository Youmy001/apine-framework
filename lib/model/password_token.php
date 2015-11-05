<?php

class ApinePasswordToken extends ApineEntityModel {
	
	private $id;
	
	private $user_id;
	
	private $token;
	
	private $creation_date;
	
	public function __construct($a_id = null) {
		
		$this->_initialize('apine_password_tokens', $a_id);
		
		if (!is_null($a_id)) {
			$this->id = $a_id;
		}
		
	}
	
	/**
	 * Fetch token's identifier
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
	 * @param string $a_token
	 * @return <string|bool>
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
	 * Fetch token's creation date
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
	 * @param string $a_timestamp
	 *        Token's creation date
	 * @return <string|bool>
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
	
	public function get_user () {
	
		if ($this->loaded == 0) {
			$this->load();
		}
	
		return $this->user;
	
	}
	
	public function set_user ($a_user) {
	
		if ($this->loaded == 0) {
			$this->load();
		}
	
		if (get_class($a_user) == 'ApineUser' || class_parents('ApineUser')) {
			$this->user = $a_user;
		} else if (is_integer($a_user) && ApineUserFactory::is_id_exist($a_user)){
			$this->user = ApineUserFactory::create_by_id($a_user);
		} else {
			return false;
		}
	
		$this->_set_field('user_id', $this->user->get_id());
		return $this->user;
	
	}
	
	/**
	 *
	 * @see ApineEntityInterface::load()
	 */
	public function load () {
	
		if (!is_null($this->id)) {
			$this->user = ApineUserFactory::create_by_id($this->_get_field('user_id'));
			$this->token = $this->_get_field('token');
			$this->creation_date = $this->_get_field('creation_date');
			$this->loaded = 1;
		}
	
	}
	
	/**
	 *
	 * @see ApineEntityInterface::save()
	 */
	public function save () {
	
		if (is_null($this->token) || is_null($this->user)) {
			throw new ApineException('Missing values', 500);
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