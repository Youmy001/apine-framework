<?php
/**
 * Basic Factory declaration.
 *
 * This file contains the factory class.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage factory
 */

/**
 * This is the implementation of the factory
 * design patern.
 */
abstract class ApineEntityFactory {

	/**
	 * Procedure to fetch every rows in a factory's scope
	 * @abstract @static
	 *
	 */
	abstract public static function create_all();
	
	/**
	 * Procedure to fetch a row in a factory's scope matching provided
	 * identifier
	 * @param string $a_id
	 *        Identifier of the row to fetch
	 * @abstract @static
	 *
	*/
	abstract public static function create_by_id($a_id);

}

/**
 * This is the implementation of the factory
 * design patern.
 * @deprecated
 */
abstract class ApineFactory extends ApineEntityFactory{}