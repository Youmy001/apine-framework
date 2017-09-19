<?php
/**
 * This script contains the interface for the entities with
 * overloading capabilities.
 *
 * @license MIT
 * @copyright 2016-2017 Tommy Teasdale
 */
namespace Apine\Entity\Overload;

/**
 * Entity interface declaration.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Entity\Overload
 */
interface EntityInterface {

	/**
	 * Procedure to save the state of an entity into database
	 *
	 * This procedure is expected to be extended in a child class
	 * when it is more complex and require additional instructions
	 * to save the state of its properties.
	 *
	 * @abstract
	 */
	public function save();

	/**
	 * Procedure to delete an entity from databases
	 *
	 * This procedure is expected to be extended in a child class
	 * when it is more complex and require additional instructions
	 * to delete other entities or clean link table.
	 *
	 * @abstract
	 */
	public function delete();

	/**
	 * Procedure to reset the state of an entity
	 *
	 * This procedure is expected to be extended in a child class
	 * when it is more complex and require additional instructions
	 * to reset its properties.
	 * @abstract
	 */
	public function reset();

}