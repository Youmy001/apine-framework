<?php
namespace Apine\Entity;
/**
 * Entity interface declaration.
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
interface EntityInterface {

	/**
	 * Loading procedure that must contain
	 * data mapper calls to load data mappers data
	 * into the entity members
	 * 
	 * @abstract
	 */
	public function load();

	/**
	 * Procedure to save an entity into database
	 * when it is more complex and needs to save
	 * other entities of write into other tables.
	 * 
	 * @abstract
	 */
	public function save();

	/**
	 * Procedure to delete an entity
	 * from database when it is more complex and
	 * needs to clean other tables or entities.
	 * 
	 * @abstract
	 */
	public function delete();

}