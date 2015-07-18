<?php
/**
 * Basic Factory declaration.
 *
 * This file contains the factory class.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage factory
 */
require_once ('lib/database.php');
require_once ('lib/liste.php');
require_once ('lib/factory/interface/factory_interface.php');

/**
 * This is the implementation of the factory
 * design patern.
 */
class ApineFactory{

	/**
	 * Databse connection instance
	 * @staticvar Database
	 */
	private static $_instance;

	/**
	 * Get a connection to database following the singleton design
	 * pattern
	 * @throws DatabaseException If cannot connect to database
	 * @return Database
	 * @static
	 *
	 */
	protected static function _get_connection(){

		if(!isset(self::$_instance)){
			try{
				self::$_instance = new Database();
			}catch(DatabaseException $e){
				throw new DatabaseException($e->getMessage());
			}
		}
		return self::$_instance;
	
	}

	/**
	 * Fetch a table row matching provided row id
	 * @param string $table_name
	 *        Name of the table where to fetch a row
	 * @param mixed $row_id
	 *        Identiifer of the row to fetch
	 * @return multitype:mixed
	 * @static
	 *
	 */
	public static function get_table_row($table_name, $row_id){

		$row_id = self::_get_connection()->quote($row_id);
		return self::_get_connection()->select("SELECT * from $table_name where ID=$row_id");
	
	}

	/**
	 * Insert a new row into a table
	 * @param string $table_name
	 *        Name of the table where to insert a row
	 * @param string[] $ar_row
	 *        Field names and values to include in the row
	 * @return string
	 * @static
	 *
	 */
	public static function set_table_row($table_name, $ar_row){

		return self::_get_connection()->insert($table_name, $ar_row);
	
	}

	/**
	 * Update a table row
	 * @param string $table_name
	 *        Name of the table where to update a row
	 * @param string[] $ar_row
	 *        Field names and values to include in the row
	 * @param string[] $ar_cond
	 *        Field names and values to match desired rows
	 * @static
	 *
	 */
	public static function update_table_row($table_name, $ar_row, $ar_cond){

		self::_get_connection()->update($table_name, $ar_row, $ar_cond);
	
	}

	/**
	 * Delete a table row
	 * @param string $table_name
	 *        Name of the table where to delete a row
	 * @param string[] $ar_cond
	 *        Field names and values to match desired rows
	 * @return boolean
	 * @static
	 *
	 */
	public static function remove_table_row($table_name, $ar_cond){

		return self::_get_connection()->delete($table_name, $ar_cond);
	
	}

}