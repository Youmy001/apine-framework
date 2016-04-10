<?php
/**
 * User Groups
 * This script contains a class to manage user groups
 *  
 * @license MIT
 * @copyright 2015 François Allard
 */
namespace Apine\User;

use Apine;

/**
 * Implementation of the database representation of users groups
 * 
 * @author François Allard <allard.f@kitaiweb.ca>
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
class UserGroup extends Apine\Entity\EntityModel {

	/**
	 * User identifier in database
	 * 
	 * @var integer
	 */
	protected $id;
	
	/**
	 * Group's name
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * Group class' constructor
	 * 
	 * @param integer $a_id
	 *        Group identifier
	 */
	public function __construct ($a_id = null) {

		$this->_initialize('apine_user_groups', $a_id);
		
		if (!is_null($a_id)) {
			$this->id = $a_id;
		}

	}
	
	/**
	 * Fetch group's identifier
	 * 
	 * @return integer
	 */
	public function get_id () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->id;

	}

	/**
	 * Set group's id
	 * 
	 * @param integer $a_id
	 *        Group's identifier
	 */
	public function set_id ($a_id) {

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('id', $a_id);

	}

	/**
	 * Fetch group's name
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
	 * Set group's name
	 * 
	 * @param string $a_name
	 *        Group's name
	 */
	 public function set_name ($a_name) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->name = $a_name;
		$this->_set_field('name', $a_name);

	}

	/**
	 * @see ApineEntityInterface::load()
	 */
	public function load () {

		if (!is_null($this->id)) {
			$this->name = $this->_get_field('name');
			$this->loaded = 1;
		}

	}

	/**
	 * @see ApineEntityInterface::save()
	 */
	public function save () {

		parent::_save();
		// Save
	}

	/**
	 * @see ApineEntityInterface::delete()
	 */
	public function delete () {

		if($this->loaded == 0) {
			$this->load();
		}
		
		parent::_destroy();
		// Remove from the database
		
	}

}