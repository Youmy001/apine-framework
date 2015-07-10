<?php
/**
 * Entity data mapper declaration.
 *
 * This file contains the entity data mapper.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package bokaro
 * @subpackage entity
 */
require_once ('lib/factory/factory.php');
require_once ('lib/entity/interface/entity_interface.php');

/**
 * This is the implementation of the data mapper
 * design patern.
 * @abstract
 *
 */
abstract class AbstractEntity implements EntityInterface{

	/**
	 * In-database entity identifier
	 * @var string
	 * @access private
	 */
	private $id;

	/**
	 * Entity Table Name
	 * @var string
	 * @access private
	 */
	private $table_name;

	/**
	 * Does Data mapper has loaded fields and values
	 * @var boolean
	 * @access private
	 */
	private $field_loaded = 0;

	/**
	 * Entity fields and values
	 * @var array
	 * @access private
	 */
	private $database_fields;

	/**
	 * Entity Modified fields and values
	 * @var array
	 * @access private
	 */
	private $modified_fields;

	/**
	 * Has fields been modified
	 * @var boolean
	 */
	private $modified;

	/**
	 * Does Entity has loaded loaded fields and
	 * values
	 * @var boolean
	 */
	protected $loaded = 0;
	// Methods
	/**
	 * Fetch database fields and values for entity
	 */
	protected function _load(){

		$this->database_fields = ($this->id !== null)?Factory::get_table_row($this->table_name, $this->id):null;
		$this->database_fields = $this->database_fields[0];
		$this->field_loaded = 1;
		if(sizeof($this->modified_fields) > 0){
			foreach($this->modified_fields as $key=>$values){
				$this->modified_fields[$key] = false;
			}
			$modified = 0;
		}
	
	}

	/**
	 * Mark entity has loaded
	 */
	protected function _force_loaded(){

		$this->field_loaded = 1;
	
	}

	/**
	 * Fetch a field's value
	 * @param string $a_field
	 *        Field name
	 * @return mixed
	 */
	protected function _get_field($a_field){
		// Load entity if not loaded yet
		if($this->field_loaded == 0)
			$this->_load();
		return $this->database_fields[$a_field];
	
	}

	/**
	 * Fetch all fields' names and values
	 * @return array
	 */
	protected function _get_all_fields(){
		// Load entity if not loaded yet
		if($this->field_loaded == 0)
			$this->_load();
		return $this->database_fields;
	
	}

	/**
	 * Get entity identifier
	 * @return string
	 */
	protected function _get_id(){

		return $this->id;
	
	}

	/**
	 * Set entity identifier
	 * @param string $id
	 *        Entity identifier
	 */
	protected function _set_id($id){

		$this->id = $id;
	
	}

	/**
	 * Get entity table name
	 * @return string
	 */
	protected function _get_table_name(){

		return $this->table_name;
	
	}

	/**
	 * Set entity table name
	 * @param string $table
	 *        Entity table name
	 */
	protected function _set_table_name($table){

		$this->table_name = $table;
	
	}

	/**
	 * Prepare Data mapper for user
	 * @param string $table_name
	 *        Entity Table name
	 * @param string $tuple_id
	 *        Entity identifier
	 */
	protected function _initialize($table_name, $tuple_id = null){

		$this->table_name = $table_name;
		$this->id = $tuple_id;
	
	}

	/**
	 * Fetch all fields' names and values
	 * @param string $field
	 *        Field name
	 * @param mixed $value
	 *        Field value
	 */
	protected function _set_field($field, $value){

		if($this->field_loaded == 0)
			$this->_load();
		$this->database_fields[$field] = $value;
		$this->modified = 1;
		$this->modified_fields[$field] = true;
	
	}

	/**
	 * Delete Entity from database
	 */
	protected function _destroy(){

		if($this->id){
			Factory::remove_table_row($this->table_name, array(
							'ID' => $this->id
			));
		}
	
	}

	/**
	 * Save Entity state to database
	 */
	protected function _save(){

		if($this->id === null){
			$this->field_loaded = 0;
		}
		if($this->field_loaded == 0){
			// This is a new entity
			$new_dbf = array();
			foreach($this->database_fields as $field=>$val){
				if(!is_numeric($field)){
					$new_dbf[$field] = $val;
				}
			}
			if(sizeof($new_dbf) > 0){
				$this->id = Factory::set_table_row($this->table_name, $new_dbf);
			}
			/*$this->field_loaded = 1;
			$this->loaded = 1;*/
			$this->_load();
		}else{
			// This is an already existing entity
			$arUpdate = array();
			foreach($this->database_fields as $key=>$value){
				if(!is_numeric($key)){
					if(isset($this->modified_fields[$key]) && $this->modified_fields[$key] == true){
						if($value == ""){
							$arUpdate[$key] = NULL;
						}else{
							$arUpdate[$key] = $value;
						}
					}
				}
			}
			Factory::update_table_row($this->table_name, $arUpdate, array(
							'ID' => $this->id
			));
		}
	
	}

}
?>
