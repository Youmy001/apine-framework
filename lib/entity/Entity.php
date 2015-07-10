<?php
/**
 * Basic entity class declaration
 * 
 * Exemple class for use of the entity data mapper
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage entity
 */
require_once ('lib/entity/AbstractEntity.php');


class Entity extends AbstractEntity{

	/**
	 * Entity constructor
	 * @param string $a_table
	 *        The table name on which the entity
	 *        is saved on
	 * @param string $a_id
	 *        identifier of the entity in the
	 *        table
	 */
	public function __construct($a_table = null, $a_id = null){

		if($a_table != null){
			$this->_initialize($a_table, $a_id);
			$this->loaded = true;
		}
	
	}

	/**
	 * Initialize the entity
	 * @param string $a_table        
	 * @param string $a_id        
	 */
	public function initialize($a_table, $a_id = null){

		if(!$this->loaded){
			$this->_initialize($a_table, $a_id);
		}else{
			if($a_id != null){
				$this->_set_id($a_id);
			}
			$this->_set_table_name($a_table);
		}
	
	}

	/**
	 * @see AbstractEntity::_get_id()
	 */
	public function get_id(){

		return $this->_get_id();
	
	}

	/**
	 * @see AbstractEntity::_set_id()
	 * @param string $a_id
	 *        Entity Identifier
	 */
	public function set_id($id){

		$this->_set_id($id);
	
	}

	/**
	 *
	 * @see AbstractEntity::_get_field()
	 * @param string $a_field        
	 * @return mixed
	 */
	public function get_field($a_field){

		if($a_table != null){
			return $this->_get_field($a_field);
		}
	
	}

	/**
	 *
	 * @see AbstractEntity::_set_field()
	 * @param string $a_field
	 *        Field Name
	 * @param mixed $a_value
	 *        Field Value
	 */
	public function set_field($a_field, $a_value){

		if($a_table != null){
			return $this->_set_field($a_field, $a_value);
		}
	
	}

	/**
	 *
	 * @see EntityInterface::load()
	 */
	public function load(){

		$this->_load;
	
	}

	/**
	 *
	 * @see EntityInterface::save()
	 */
	public function save(){

		$this->_save();
	
	}

	/**
	 *
	 * @see EntityInterface::delete()
	 */
	public function delete(){

		$this->_destroy();
	
	}

}

