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
 *
 */
abstract class EntityModel implements EntityInterface {

	/**
	 * In-database entity identifier
	 * 
	 * @var string
	 * @access private
	 */
	private $id;

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
	 * @var string $load_field
	 */
	private $load_field;
	
	/**
	* Fetch database fields and values for entity
	*/
	final protected function _load () {
		
		$db = new Database();
		
		if ($this->id !== null) {
			if (!is_numeric($this->id)) {
				$field_id = $db->quote($this->id);
			} else {
				$field_id = $this->id;
			}
			
			$database_fields = $db->select("SELECT * from $this->table_name where $this->load_field = $field_id");
			
			if ($database_fields) {
				$this->database_fields = $database_fields[0];
				$this->field_loaded = 1;
			}
		}
		
		if (sizeof($this->modified_fields) > 0) {
			foreach ($this->modified_fields as $key => $values) {
				$this->modified_fields[$key] = false;
			}
			
			$modified = 0;
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
			$this->_load();
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
			$this->_load();
		}
		
		return $this->database_fields;

	}

	/**
	 * Get entity identifier
	 * 
	 * @return string
	 */
	final protected function _get_id () {

		return $this->id;

	}

	/**
	 * Set entity identifier
	 * 
	 * @param string $id
	 *        Entity identifier
	 */
	final protected function _set_id ($id) {

		$this->id = $id;

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
	 */
	final protected function _initialize ($table_name, $tuple_id = null, $field_name = "id") {

		$this->table_name = $table_name;
		$this->id = $tuple_id;
		$this->load_field = $field_name;
		
		//echo get_class($this)."\n";
		//print_r(get_class_vars('ApineUser'));

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
			$this->_load();
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
		
		if ($this->id) {
			$db->delete($this->table_name, array(
							$this->load_field => $this->id
			));
		}

	}

	/**
	 * Save Entity state to database
	 */
	final protected function _save () {
		
		$db = new Database();

		if ($this->id === null) {
			$this->field_loaded = 0;
		}
		
		if($this->field_loaded == 0) {
			
			// This is a new entity
			$new_dbf = array();
			
			foreach ($this->database_fields as $field => $val) {
				if (!is_numeric($field)) {
					$new_dbf[$field] = $val;
				}
			}
			
			if (sizeof($new_dbf) > 0) {
				$this->id = $db->insert($this->table_name, $new_dbf);
			}
			
			$this->_load();
		} else {
			// This is an already existing entity
				
			// Update procedure only executed if at least
			// one field was modified
			if (count($this->modified_fields) > 0) {
				$arUpdate = array();
			
				foreach ($this->database_fields as $key => $value) {
					if (!is_numeric($key)) {
						if (isset($this->modified_fields[$key]) && $this->modified_fields[$key] == true) {
							if ($value == "") {
								$arUpdate[$key] = NULL;
							} else {
								$arUpdate[$key] = $value;
							}
						}
					}
				}
			
				$db->update($this->table_name, $arUpdate, array(
								$this->load_field => $this->id
				));
			}
		}

	}

}