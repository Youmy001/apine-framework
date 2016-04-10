<?php

namespace Apine\Entity;

/**
* Basic entity with no domain logic
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
	 */
	public function __construct ($a_table = null, $a_id = null, $a_field = "id") {

		if ($a_table != null) {
			$this->_initialize($a_table, $a_id, $a_field);
			$this->loaded = true;
		}

	}

	/**
	 * Initialize the entity
	 *
	 * @param string $a_table
	 * @param string $a_id
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
	public function set_id ($id) {

		$this->_set_id($id);

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
		}

	}

	/**
	 *
	 * @see ApineEntityModel::_set_field()
	 * @param string $a_field
	 *        Field Name
	 * @param mixed $a_value
	 *        Field Value
	 */
	public function set_field ($a_field, $a_value) {

		if ($a_field != null) {
			return $this->_set_field($a_field, $a_value);
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