<?php
/**
 * This file contains the user property class
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\User;

use Apine\Entity as Entity;

/**
 * Implementation of the database representation of user properties
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 */
final class Property extends Entity\EntityModel {

    /**
     * @var integer
     */
	private $id;

    /**
     * @var User
     */
	private $user;

    /**
     * @var string
     */
	private $name;

    /**
     * @var mixed
     */
	private $value;

    /**
     * Property constructor.
     *
     * @param integer $a_id
     */
	public function __construct($a_id = null) {
		
		$this->_initialize('apine_user_properties', $a_id);
		
		if (!is_null($a_id)) {
			$this->id = $a_id;
		}
		
	}
	
	/**
	 * Fetch property's identifier
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
	 * Set property's id
	 * 
	 * @param integer $a_id
	 *        Property's identifier
	 */
	final public function set_id ($a_id) {

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('id', $a_id);
	
	}
	
	/**
	 * Fetch the token user
	 *
	 * @return User
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
	 * @param User|integer $a_user
	 * @return User
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
			return null;
		}
	
		$this->_set_field('user_id', $this->user->get_id());
		return $this->user;
	
	}

    /**
     * Fetch the property name
     *
     * @return string
     */
	public function get_name () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->name;
		
	}

    /**
     * Set the name of the property
     *
     * @param string $a_name
     */
	public function set_name ($a_name) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->name = $a_name;
		$this->_set_field('name', $a_name);
		
	}

    /**
     * Fetch the value of the property
     *
     * @return mixed
     */
	public function get_value () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->value;
		
	}

    /**
     * Set the value of the property
     *
     * @param mixed $a_value
     */
	public function set_value ($a_value) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		if ( null !== $value = serialize($a_value)) {
			$this->value = $a_value;

			if (!is_null($a_value)) {
				$this->_set_field('value', serialize($a_value));
			} else {
				$this->_set_field('value', null);
			}
		}
		
	}

    /**
     * @see Entity\EntityInterface::load()
     */
	public function load () {
		
		if (!is_null($this->id)) {
			$this->user = Factory\UserFactory::create_by_id($this->_get_field('user_id'));
			$this->name = $this->_get_field('name');
		
			if (@unserialize($this->_get_field('value')) !== false) {
				$this->value = @unserialize($this->_get_field('value'));
			} else {
				$this->value = $this->_get_field('value');
			}
		}
		
	}

    /**
     * @see Entity\EntityInterface::save()
     */
	public function save () {
		
		parent::_save();
		$this->set_id($this->_get_id());
		
	}

    /**
     * @see Entity\EntityInterface::delete()
     */
	public function delete () {
		
		parent::_destroy();
		
	}
	
}