<?php

/**
 * Factory interface declaration.
 *
 * This file contains the interface of the factory.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package bokaro
 * @subpackage factory
 */
interface FactoryInterface{

	/**
	 * Procedure to fetch every rows in a factory's scope
	 * @abstract @static
	 *          
	 */
	public static function create_all();

	/**
	 * Procedure to fetch a row in a factory's scope matching provided
	 * identifier
	 * @param string $a_id
	 *        Identifier of the row to fetch
	 * @abstract @static
	 *          
	 */
	public static function create_by_id($a_id);

}