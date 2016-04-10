<?php
/**
 * Basic Factory declaration.
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Entity;

/**
 * This is the implementation of the factory
 * design patern.
 */
interface EntityFactoryInterface {

	/**
	 * Procedure to fetch every rows in a factory's scope
	 * 
	 * @static
	 */
	public static function create_all();
	
	/**
	 * Procedure to fetch a row in a factory's scope matching provided
	 * identifier
	 * 
	 * @param string $a_id
	 *        Identifier of the row to fetch
	 * @static
	*/
	public static function create_by_id($a_id);

}