<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/13
 * Time: 17:25
 */

namespace Apine\Entity;

/**
 * Class OverloadTrait
 * @package Apine\Entity
 * @uses EntityModel
 */
trait OverloadTrait {

	protected $field_mapping;

	final public function __call ($a_name, $a_arguments) {

		if (!$this->_is_loaded()) {
			$this->field_mapping = $this->load();
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

					if ($property === $this->_get_primary_key()) {
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

	/**
	 * Load database values into entity properties
	 */
	final protected function _property_load () {

		if (!$this->_is_loaded()) {
			if (!$this->_is_field_loaded()) {
				$this->_load();
			}

			if ($this->_get_id() !== null) {
				foreach ($this->_get_all_fields() as $name => $value) {
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

	/*public function load () {

	}

	public function save () {
		parent::save();

		if (property_exists($this, $this->_get_primary_key())) {
			$this->set_{$this->_get_primary_key()} = $this->_get_id();
		}
	}

	public function delete () {
		parent::delete();
	}*/
}