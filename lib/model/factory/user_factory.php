<?php
/**
 * User Factory declaration.
 *
 * This file contains the user factory class.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */
require_once ('lib/factory/factory.php');
require_once ('lib/model/user.php');

class ApineUserFactory extends ApineFactory implements ApineFactoryInterface{

	/**
	 * Verify if the identifier exists
	 * @param integer $user_id        
	 * @return boolean
	 */
	public static function is_id_exist($user_id){

		$id_sql = self::_get_connection()->select("SELECT ID FROM users WHERE ID=$user_id");
		if($id_sql){
			return true;
		}
		return false;
	
	}

	/**
	 * Verify if a user uses the provided name
	 * @param string $user_name
	 *        User username
	 * @return boolean
	 */
	public static function is_name_exist($user_name){

		$id_sql = self::_get_connection()->select("SELECT ID FROM users WHERE name='$user_name'");
		if($id_sql){
			return true;
		}
		return false;
	
	}

	/**
	 * Verify if a user uses the provided email address
	 * @param string $user_mail
	 *        User email address
	 * @return boolean
	 */
	public static function is_email_exist($user_mail){

		$id_sql = self::_get_connection()->select("SELECT ID FROM users WHERE email='$user_mail'");
		if($id_sql){
			return true;
		}
		return false;
	
	}
	/**
	 *
	 * @return Liste
	 */
	public static function create_all(){

		$request = self::_get_connection()->select('SELECT ID from `users` ORDER BY `name`');
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new Apine_User($item['ID']));
			}
		}
		return $liste;
	
	}

	/**
	 *
	 * @param integer $a_id
	 *        User Identifier
	 * @return User
	 */
	public static function create_by_id($a_id){

		$user_sql_id = self::_get_connection()->prepare('SELECT ID FROM `users` WHERE ID=?');
		$ar_user_sql = self::_get_connection()->execute(array(
						$a_id
		), $user_sql_id);
		if($ar_user_sql){
			$return = new Apine_User($ar_user_sql[0]['ID']);
		}else{
			$return = null;
		}
		return $return;
	
	}

	/**
	 * Fetch a user by username
	 * @param string $name
	 *        User username
	 * @return User
	 */
	public static function create_by_name($name){

		$user_sql_id = self::_get_connection()->prepare('SELECT ID FROM `users` WHERE name=?');
		$ar_user_sql = self::_get_connection()->execute(array(
						$name
		), $user_sql_id);
		if($ar_user_sql){
			$user_id = end($ar_user_sql);
			$user_id = $user_id['ID'];
			$lang = new Apine_User($user_id);
		}else{
			$lang = null;
		}
		return $lang;
	
	}

	/**
	 * Fetch users by permission level
	 * @param integer $access
	 *        User Permission level
	 * @return Liste
	 */
	public static function create_by_access_right($access){

		$request = self::_get_connection()->select("SELECT ID from `users` where type=$access");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new Apine_User($item['ID']));
			}
		}
		return $liste;
	
	}
	
	/**
	 * Authentifiate a user with a combination of a user name and an
	 * encoded password.
	 *
	 * @param string $name
	 *        Username
	 * @param string $pass
	 *        Encrypted password
	 * @return integer
	 */
	public static function authentication($name, $pass){

		$connect_sql_id = self::_get_connection()->prepare('SELECT ID FROM users WHERE name=? AND pwd=?');
		$ar_connect_sql = self::_get_connection()->execute(array(
						$name,
						$pass
		), $connect_sql_id);
		if($ar_connect_sql){
			$connect = end($ar_connect_sql);
			$connect = $connect['ID'];
		}else{
			$connect = 0; // Value of false
		}
		return $connect;
	
	}

}