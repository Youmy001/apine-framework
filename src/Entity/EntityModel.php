<?php
/**
 * Entity data mapper and basic entity class declaration
 * with method overloading capabilities.
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\Entity;

use Apine\Core\Database;
use Apine\Utility\Types;

/**
 * Simple data mapper implementation with method
 * overloading capabilities.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Entity
 * @abstract
 *
 * @method integer get_id() Return the value of the primary key
 * @method set_id(integer $integer) Assign a new value to the primary key
 * @method load() Procedure to load more complex properties or properties that are not directly represented in the database. Optional.
 */
abstract class EntityModel implements EntityInterface {

	/**
	 * Name of the primary key column
	 * @var string
	 */
	private $key_field = 'id';

	/**
	 * Value of the primary key
	 * @var mixed
	 */
	private $key_value;

	/**
	 * Name of the database table
	 * @var string
	 */
	private $table_name;

	/**
	 * Has the loader filled entity's properties
	 * @var bool
	 */
	private $values_loaded = false;

	/**
	 * Value of the primary key when loading the table row
	 * @var mixed
	 */
	private $initial_key_value;

	/**
	 * Values of the table row
	 * @var array
	 */
	private $initial_values = array();

	/**
	 * Values to be committed in the database table
	 * @var array
	 */
	private $edited_values = array();

	/**
	 * Mapping of columns with their related property
	 *
	 * The mapping works the following way :
	 * "column_name" => "property_name"
	 *
	 * @var array
	 */
	protected $field_mapping = array();

	/**
	 * Instance of the database connection
	 * @var Database
	 */
	private $database;

	/**
	 * Method Overloading
	 *
	 * This is a replacement for the custom getter and
	 * setter that were required to be written. In most
	 * cases, these were more or less.
	 *
	 * It accepts the following method names:
	 *  - set_&lt;name_of_property&gt;
	 *  - get_&lt;name_of_property&gt;
	 *
	 * The "set" action will assign the new value to the
	 * property and update the data mapper.
	 *
	 * The "get" action returns the current value of the
	 * property.
	 *
	 * @param string $a_name Name of the method called
	 * @param array $a_arguments
	 * 				Parameters passed to the method
	 * @return mixed
	 * 				Value of the property when calling
	 * 				the get action
	 * @throws \Exception
	 * 				If the property does not exist or the
	 * 				method name does not exist
	 */
	final public function __call ($a_name, $a_arguments) {

		/*
		 * Load the properties if they were not already
		 */
		if (!$this->values_loaded) {
			$this->property_loading();
		}

		/*
		 * Extract the action name and the name of the
		 * property from the method name
		 */
		$action = strtolower(substr($a_name, 0, 3));
		$property = strtolower(substr($a_name, 4));

		$properties = get_class_vars(get_class($this)); // List of accessible properties

		try {
			switch ($action) {
				// Fetch the value of the property
				case 'get':
					if ((property_exists($this, $property) && array_key_exists($property, $properties)) || $property === 'id') {
						if ($property === 'id') {
							$return = $this->key_value;
						} else {
							$return = $this->{$property};
						}

						return $return;
					} else {
						$trace = debug_backtrace();
						throw new \Exception('Undefined property  ' . $property . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);
					}

					break;
				// Set the value to the property
				case 'set':
					if ((property_exists($this, $property) && array_key_exists($property, $properties)) || $property === 'id') {
						/*
						 * The primary key has its own property from the data mapper
						 * If the property name passed is "id" or the name of the
						 * primary key column its value will be modified.
						 */
						if ($property === 'id' || $property === $this->key_field) {
							$this->key_value = $a_arguments[0];
							$this->set($this->key_field, $a_arguments[0]);
						}

						if (property_exists($this, $property)) {
							$this->{$property} = $a_arguments[0];

							if ($property !== 'id' && $property !== $this->key_field) {	// Prevent to execute the modification to the database
																						// entry twice if the property is the primary key
								if (is_array($this->field_mapping) && (false !== ($field = array_search($property, $this->field_mapping)))) {
									$this->set($field, $a_arguments[0]);
								} else {
									$this->set($property, $a_arguments[0]);
								}
							}
						}
					} else {
						$trace = debug_backtrace();
						throw new \Exception('Undefined property  ' . $property . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);
					}
					break;
				default:
					$trace = debug_backtrace();
					throw new \Exception('Undefined Method ' . $a_name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);
			}
		} catch (\Exception $e) {
			trigger_error($e->getMessage(), E_USER_NOTICE);
		}

		return null;

	}

	/**
	 * @see EntityInterface::save()
	 */
	public function save () {
		$this->commit();
	}

	/**
	 * @see EntityInterface::delete()
	 */
	public function delete () {
		$this->remove();
	}

	/**
	 * @see EntityInterface::reset()
	 */
	public function reset () {
		$this->rollback();
	}

	/**
	 * Load the table row into the entity
	 *
	 * @throws \Exception If the entity is not initialized yet
	 */
	final private function data_loading () {

		if ($this->table_name === null && $this->key_field === null) {
			throw new \Exception('Entity not initialized');
		}

		/*
		 * If the initial values are empty, the values were not fetched already.
		 * Values cannot be loaded if the primary key is null.
		 */
		if (empty($this->initial_values) && $this->key_value !== null) {
			$request = $this->database->prepare("SELECT * FROM `$this->table_name` WHERE $this->key_field = ?");
			$response = $this->database->execute(array($this->key_value), $request);

			if ($response) {
				$this->initial_values = $response[0];
			}
		}

	}

	/**
	 * Load the table row values into corresponding properties
	 */
	final private function property_loading () {

		if (!$this->values_loaded) {
			$this->data_loading();
			$properties = get_class_vars(get_class($this)); // List of accessible properties

			foreach ($this->initial_values as $name => $value) {
				if (isset($this->field_mapping[$name])) {
					$field = $name;
					$name = $this->field_mapping[$name];
				} else {
					$field = $name;
				}

				if (property_exists($this, $name) && array_key_exists($name, $properties)) {	// Should try to assign the value only
																								// If the property is accessible
					$this->{$name} = $this->get($field);
				}
			}

			$this->values_loaded = true;	// Mark the properties as loaded
		}

	}

	/**
	 * Save the current state of the entity to the database
	 *
	 * @throws \Exception If the entity is not initialized yet
	 * @throws \Exception If the query execution failed
	 */
	final private function commit () {

		if ($this->table_name === null && $this->key_field === null) {
			throw new \Exception('Entity not initialized');
		}

		/*
		 * Concatenate the initial state with the updated state.
		 * The result will contain all the columns for the row.
		 */
		$array_values = array_merge($this->initial_values, $this->edited_values);

		// Cannot save if the entity is empty or not modified
		if (!empty($array_values) && !empty($this->edited_values)) {
			$fields = array_keys($array_values);	// Array of all column names
			$values = array_values($array_values);	// Array of values

			//$initial_fields = array_keys($this->initial_values);
			//$initial_values = array_keys($this->initial_values);
			$edited_fields = array_keys($this->edited_values);

			/*
			 * Build the query. It is a MySQL INSERT ON DUPLICATE KEY UPDATE request
			 */
			$query = "INSERT INTO `{$this->table_name}` (";
			$query .= join(', ', $fields) . ") VALUES (";

			for ($i = 0; $i < count($values); $i++) {
				$query .= '?';

				if ($i !== (count($values) - 1)) {
					$query .= ', ';
				}
			}

			$query .= ') ON DUPLICATE KEY UPDATE';

			for ($k = 0; $k < count($edited_fields); $k++) {
				$query .= " {$edited_fields[$k]} = VALUES(`{$edited_fields[$k]}`)";

				if ($k !== (count($edited_fields) - 1)) {
					$query .= ',';
				}
			}

			$query .= ";";

			/*
			 * Execute the query
			 */
			try {
				$request = $this->database->prepare($query);
				$response = $this->database->execute($values, $request);

				if ($response == '1') {
					$id = $this->database->last_insert_id();

					if ($this->key_value === null) {
						$this->set_id($id);
					}

					/*
					 * Update the initial state and clear the current state
					 */
					$this->initial_values = array_merge($this->initial_values, $this->edited_values);
					$this->initial_key_value = $id;
					$this->edited_values = array();
				}
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage(), $e->getCode(), $e);
			}

		}

	}

	/**
	 * Delete the entry of the entity from the database
	 *
	 * @throws \Exception If the entity is not initialized yet
	 */
	final private function remove () {

		if ($this->table_name === null && $this->key_field === null) {
			throw new \Exception('Entity not initialized');
		}

		if ($this->key_value !== null) {
			try {
				$this->database->delete($this->table_name, array($this->key_field => $this->key_value));

				/**
				 * Reset the entity to a unsaved state
				 */
				$this->set_id(null); // Reset the property for the primary key
				$this->edited_values = array_merge($this->initial_values, $this->edited_values);
				unset($this->edited_values[$this->key_field]); // Remove the primary key value
				$this->initial_values = array();
				$this->values_loaded = false;    // Values are not loaded because the primary key is null
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage(), $e->getCode(), $e);
			}
		}

	}

	/**
	 * Revert the entity to its initial state.
	 */
	final private function rollback () {

		/*
		 * Revert the primary key and erase the edited values
		 */
		$this->key_value = $this->initial_key_value;
		$this->edited_values = array();
		$this->values_loaded = false;

		$self_properties = get_class_vars(__CLASS__);
		$entity_properties = get_class_vars(get_class($this));

		/*
		 * Set all the properties of the child class back to NULL
		 */
		foreach ($entity_properties as $key => $value) {
			if (!array_key_exists($key, $self_properties) && $key !== 'field_mapping') {
				$this->{$key} = null;
			}
		}

		$this->property_loading(); // Reload the properties

		// Execute the custom loading method
		if (method_exists($this, 'load')) {
			$this->load();
		}

	}

	/**
	 * Fetch the current value of a field
	 *
	 * @param string $a_field Name of the field
	 * @return mixed
	 */
	final protected function get ($a_field) {

		if (empty($this->initial_values)) {
			$this->data_loading();
		}

		$value = null;	// Return NULL if field is not found
		$values_array = array_merge($this->initial_values, $this->edited_values);	// Concatenate the edited and initial values
																					// to get a list of every current values

		if (isset($values_array[$a_field])) {
			$value = $values_array[$a_field];

			/*
			 * Normalize the time values to the UTC+0 timezone.
			 * This allows many instances of APIne Framework located
			 * in different timezones and using Entities to connect
			 * to the same database and have coherent time value.
			 *
			 * It add the current offset to the UTC+0 timezone to
			 * valid UNIX timestamps.
			 */
			if (Types::is_timestamp($value) && !is_numeric($value)) {
				$datetime = new \DateTime('now');
				$time = strtotime($value);
				$time += $datetime->getOffset();
				$value = date('Y-m-d H:i:s', $time);
			}
		}

		return $value;

	}

	/**
	 * Return every field names and their values
	 *
	 * @return array
	 */
	final protected function get_all () {

		if (empty($this->initial_values)) {
			$this->data_loading();
		}

		return array_merge($this->initial_values, $this->edited_values); // Concatenate the initial state with the updated state.

	}

	/**
	 * Prepare the data mapper for use
	 *
	 * @param string $a_table_name Name of the table
	 * @param string|integer $a_id Value of the primary key of the entity in the database
	 * @param string $a_field Name of the column marked as a primary key
	 * @throws \Exception If the table name of the column name are not valid
	 */
	final protected function initialize ($a_table_name, $a_id, $a_field = 'id') {

		// Regular expression matching valid table names and column names
		$regex = '/^([^0-9])([0-9a-zA-Z$_\x{0080}-\x{FFFF}]{1,64})$/u';

		if(preg_match($regex, $a_table_name) === false) {
			throw new \Exception('Invalid Table Name');
		}

		if (preg_match($regex, $a_field) === false) {
			throw new \Exception('Invalid Column Name');
		}

		$this->key_field = $a_field;
		$this->key_value = $a_id;
		$this->initial_key_value = $a_id;
		$this->table_name = $a_table_name;
		$this->database = new Database();

		// Execute the custom loading method
		if (method_exists($this, 'load')) {
			$this->load();
		}

	}

	/**
	 * Set the value of a field
	 *
	 * @param string $a_field Name of the field to modify
	 * @param mixed $a_value Value to assign in the field
	 */
	final protected function set ($a_field, $a_value) {

		/*
		 * Normalize the time values to the UTC+0 timezone.
		 * This allows many instances of APIne Framework located
		 * in different timezones and using Entities to connect
		 * to the same database and have coherent time value.
		 *
		 * It subtract the current offset to the UTC+0 timezone to
		 * valid UNIX timestamps. This has the effect of bringing
		 * back the timestamp to current timezone.
		 */
		if (Types::is_timestamp($a_value) && !is_numeric($a_value)) {
			$datetime = new \DateTime('now');
			$time = strtotime($a_value);
			$time -= $datetime->getOffset();
			$value = date("Y-m-d H:i:s", $time);
		} else {
			$value = $a_value;
		}

		$this->edited_values[$a_field] = $value;

	}

}