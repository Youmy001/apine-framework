<?php
/**
 * Generic Entity
 *
 * @license MIT
 * @copyright 2015-2017 Tommy Teasdale
 */
namespace Apine\Entity;

use Apine\Core\Database;

/**
 * Basic entity with no domain logic
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Entity
 */
class BasicEntity extends EntityModel {

	/**
	 * Entity constructor
	 *
	 * @param string $a_table
	 *        The table name on which the entity
	 *        is saved on
	 * @param string $a_id
	 *        identifier of the entity in the
	 *        table
     * @param string $a_field
     *        Name of the primary key column
	 * @param Database $database
	 *        Instance of a database to commit changes to
	 */
	public function __construct ($a_table = null, $a_id = null, $a_field = "id", Database $database) {

		if ($a_table != null) {
			$this->_initialize($a_table, $a_id, $a_field, $database);
			$this->loaded = true;
		}

	}

	/**
	 * Initialize the entity
	 *
	 * @param string $a_table
	 * @param string $a_id
     * @param string $a_field
	 */
	public function initialize ($a_table, $a_id = null , $a_field = "id") {

		if (!$this->loaded) {
			$this->_initialize($a_table, $a_id, $a_field);
		} else {
				
			if ($a_id != null) {
				$this->_set_id($a_id);
			}
				
			$this->_set_table_name($a_table);
		}

	}

	/**
	 * @see ApineEntityModel::_get_id()
	 */
	public function get_id () {

		return $this->_get_id();

	}

	/**
	 * @see ApineEntityModel::_set_id()
	 * @param string $a_id
	 *        Entity Identifier
	 */
	public function set_id ($a_id) {

		$this->_set_id($a_id);

	}

	/**
	 *
	 * @see ApineEntityModel::_get_field()
	 * @param string $a_field
	 * @return mixed
	 */
	public function get_field ($a_field) {

		if ($a_field != null) {
			return $this->_get_field($a_field);
		} else {
		    return null;
        }

	}

	/**
	 *
	 * @see ApineEntityModel::_set_field()
	 * @param string $a_field
	 *        Field Name
	 * @param mixed $a_value
	 *        Field Value
     * @return mixed
	 */
	public function set_field ($a_field, $a_value) {

		if ($a_field != null) {
			return $this->_set_field($a_field, $a_value);
		} else {
		    return null;
        }

	}

	/**
	 *
	 * @see ApineEntityInterface::load()
	 */
	public function load () {

		$this->_load();

	}

	/**
	 *
	 * @see ApineEntityInterface::save()
	 */
	public function save () {

		$this->_save();

	}

	/**
	 *
	 * @see ApineEntityInterface::delete()
	 */
	public function delete () {

		$this->_destroy();

	}

}