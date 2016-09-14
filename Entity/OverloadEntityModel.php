<?php
/**
 * Entity data mapper and basic entity class declaration
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Entity;

use \DateTime;
use Apine\Core\Database;

/**
 * This is the implementation of the data mapper
 * design patern.
 * 
 * @abstract
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Entity
 */
abstract class OverloadEntityModel implements EntityInterface {

	/**
	 * In-database entity identifier
	 *
	 * @var string
	 * @access private
	 */
	private $entity_id;

	/**
	 * Entity Table Name
	 *
	 * @var string
	 * @access private
	 */
	private $table_name;

	/**
	 * Does Data mapper has loaded fields and values
	 *
	 * @var boolean
	 * @access private
	 */
	private $field_loaded = 0;

	/**
	 * Entity fields and values
	 *
	 * @var array
	 * @access private
	 */
	private $database_fields;

	/**
	 * Entity Modified fields and values
	 *
	 * @var array
	 * @access private
	 */
	private $modified_fields;

	/**
	 * Has fields been modified
	 *
	 * @var boolean
	 */
	private $modified;

	/**
	 * Does Entity has loaded loaded fields and
	 * values
	 *
	 * @var boolean
	 */
	protected $loaded = 0;

	/**
	 * Name of the primary key field
	 *
	 * @var string $primary_key
	 */
	private $primary_key;

	protected $field_mapping = array();

	final public function __call ($a_name, $a_arguments) {

		if (!$this->loaded) {
			$this->_property_load();
		}

		$action = substr($a_name, 0, 3);
		$property = strtolower(substr($a_name, 4));

		switch ($action) {
			case 'get':
				if (property_exists($this, $property)) {
					return $this->{$property};
				} else {
					$trace = debug_backtrace();
					trigger_error('Undefined property  ' . $a_name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
					return null;
				}
				break;
			case 'set':
				if (property_exists($this, $property)) {
					$this->{$property} = $a_arguments[0];

					if (is_array($this->field_mapping) && (false !== ($field = array_search($property, $this->field_mapping)))) {
						$this->_set_field($field, $a_arguments[0]);
					} else {
						$this->_set_field($property, $a_arguments[0]);
					}

					if ($property === $this->primary_key) {
						$this->_set_id($a_arguments[0]);
					}
				} else {
					$trace = debug_backtrace();
					trigger_error('Undefined property  ' . $a_name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
					return null;
				}
				break;
			default:
				return false;
		}

	}

	final protected function _load () {
		$this->_property_load();
	}

	/**
	 * Fetch database fields and values for entity
	 */
	final protected function _database_load () {

		$db = new Database();

		if ($this->entity_id !== null) {
			if (!is_numeric($this->entity_id)) {
				$field_id = $db->quote($this->entity_id);
			} else {
				$field_id = $this->entity_id;
			}

			$database_fields = $db->select("SELECT * from $this->table_name where $this->primary_key = $field_id");

			if ($database_fields) {
				$this->database_fields = $database_fields[0];
				$this->field_loaded = 1;
			}
		}

		if (sizeof($this->modified_fields) > 0) {
			foreach ($this->modified_fields as $key => $values) {
				$this->modified_fields[$key] = false;
			}
		}

	}

	/**
	 * Load database values into entity properties
	 */
	final protected function _property_load () {

		if (!$this->loaded) {
			if ($this->field_loaded == 0) {
				$this->_database_load();
			}

			if ($this->entity_id !== null) {
				foreach ($this->database_fields as $name => $value) {
					if (is_array($this->field_mapping) && isset($this->field_mapping[$name])) {
						$field = $name;
						$name = $this->field_mapping[$name];
						//$this->{$this->field_mapping[$name]} = $this->_get_field($name);
					} else {
						$field = $name;
					}

					if (property_exists($this, $name) && empty($this->{$name})) {
						$this->{$name} = $this->_get_field($field);
					}
				}

				$this->loaded = 1;
			}
		}

	}

	/**
	 * Mark entity has loaded
	 */
	final protected function _force_loaded () {

		$this->field_loaded = 1;

	}

	/**
	 * Verify if the entity is loaded
	 *
	 * @return boolean
	 */
	final protected function _is_loaded () {

		return (bool) $this->loaded;

	}

	/**
	 * Fetch a field's value
	 *
	 * @param string $a_field
	 *        Field name
	 * @return mixed
	 */
	final protected function _get_field ($a_field) {

		// Load entity if not loaded yet
		if ($this->field_loaded == 0) {
			$this->_database_load();
		}

		if (isset($this->database_fields[$a_field])) {
			if (is_timestamp($this->database_fields[$a_field]) && !is_numeric($this->database_fields[$a_field])) {
				$datetime = new DateTime('now');
				$time = strtotime($this->database_fields[$a_field]);
				$time += $datetime->getOffset();
				return date("Y-m-d H:i:s", $time);
			} else {
				return $this->database_fields[$a_field];
			}
		} else  {
			return null;
		}

	}

	/**
	 * Fetch all fields' names and values
	 *
	 * @return array
	 */
	final protected function _get_all_fields () {

		// Load entity if not loaded yet
		if ($this->field_loaded == 0) {
			$this->_database_load();
		}

		return $this->database_fields;

	}

	/**
	 * Get entity identifier
	 *
	 * @return string
	 */
	final protected function _get_id () {

		return $this->entity_id;

	}

	/**
	 * Set entity identifier
	 *
	 * @param string $id
	 *        Entity identifier
	 */
	final protected function _set_id ($id) {

		$this->entity_id = $id;

	}

	/**
	 * Get entity table name
	 *
	 * @return string
	 */
	final protected function _get_table_name () {

		return $this->table_name;

	}

	/**
	 * Set entity table name
	 *
	 * @param string $table
	 *        Entity table name
	 */
	final protected function _set_table_name ($table) {

		$this->table_name = $table;

	}

	/**
	 * Prepare Data mapper for user
	 *
	 * @param string $table_name
	 *        Entity Table name
	 * @param string $tuple_id
	 *        Entity identifier
	 * @param string $field_name
	 *        Name of the primary key column
	 */
	final protected function _initialize ($table_name, $tuple_id = null, $field_name = "id") {

		$this->table_name = $table_name;
		$this->entity_id = $tuple_id;
		$this->primary_key = $field_name;

		if (property_exists($this, $field_name)) {
			$this->{$field_name} = $tuple_id;
		}

	}

	/**
	 * Fetch all fields' names and values
	 *
	 * @param string $field
	 *        Field name
	 * @param mixed $value
	 *        Field value
	 */
	final protected function _set_field ($field, $value) {

		if ($this->field_loaded == 0) {
			$this->_database_load();
		}

		if (is_timestamp($value) && !is_numeric($value)) {
			$datetime = new DateTime('now');
			$time = strtotime($value);
			$time -= $datetime->getOffset();
			$value = date("Y-m-d H:i:s", $time);
		}

		$this->database_fields[$field] = $value;
		$this->modified = 1;
		$this->modified_fields[$field] = true;

	}

	/**
	 * Delete Entity from database
	 */
	final protected function _destroy () {

		$db = new Database();

		if ($this->entity_id) {
			$db->delete($this->table_name, array(
				$this->primary_key => $this->entity_id
			));
		}

	}

	/**
	 * Save Entity state to database
	 */
	final private function _save () {

		$db = new Database();

		if ($this->entity_id === null) {
			$this->field_loaded = 0;
		}

		if($this->field_loaded == 0) {
			// This is a new or unloaded entity
			$new_dbf = array();

			if (!empty($this->database_fields)) {
				foreach ($this->database_fields as $field => $val) {
					if (!is_numeric($field)) {
						$new_dbf[$field] = $val;
					}
				}
			}

			if (sizeof($new_dbf) > 0) {
				$new_id = $db->insert($this->table_name, $new_dbf);

				if (property_exists($this, $this->primary_key)) {
					$this->set_{$this->primary_key} = $new_id;
				} else {
					$this->entity_id = $new_id;
				}
			}

			$this->_database_load();
		} else {
			// This is an already existing entity

			// Update procedure only executed if at least
			// one field was modified
			if (count($this->modified_fields) > 0) {
				$arUpdate = array();

				foreach ($this->database_fields as $key => $value) {
					if (!is_numeric($key)) {
						if (isset($this->modified_fields[$key]) && $this->modified_fields[$key] == true) {
							$arUpdate[$key] = $value;
						}
					}
				}

				$db->update($this->table_name, $arUpdate, array(
					$this->primary_key => $this->entity_id
				));
			}
		}

	}

	public function load () {}

	public function save () {

		$this->_save();

	}

	public function delete () {

		$this->_destroy();

	}

}