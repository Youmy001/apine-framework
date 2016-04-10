<?php
/**
 * User Factory declaration.
 *
 * This file contains the user factory class.
 *  
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\User\Factory;

use Apine;

class UserFactory implements Apine\Entity\EntityFactoryInterface {

	/**
	 * Verify if the identifier exists
	 * 
	 * @param integer $user_id        
	 * @return boolean
	 */
	public static function is_id_exist ($user_id) {

		/*$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_users` WHERE `id`=$user_id  AND `type`<>10");*/
		$id_sql = (new Apine\Core\Database())->select("SELECT `id` FROM `apine_users` WHERE `id` = $user_id  AND `type` <> 10");
		
		if ($id_sql) {
			return true;
		}
		
		return false;
	
	}

	/**
	 * Verify if a user uses the provided name
	 * 
	 * @param string $user_name
	 *        User username
	 * @return boolean
	 */
	public static function is_name_exist ($user_name){

		/*$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_users` WHERE `username`='$user_name' OR `email`='$user_name'  AND `type`<>10");*/
		$id_sql = (new Apine\Core\Database())->select("SELECT `id` FROM `apine_users` WHERE `username` = '$user_name' OR `email` = '$user_name'  AND `type` <> 10");
		if ($id_sql) {
			return true;
		}
		
		return false;
	
	}

	/**
	 * Verify if a user uses the provided email address
	 * 
	 * @param string $user_mail
	 *        User email address
	 * @return boolean
	 */
	public static function is_email_exist ($user_mail) {

		/*$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_users` WHERE `email`='$user_mail'  AND `type`<>10");*/
		$id_sql = (new Apine\Core\Database())->select("SELECT `id` FROM `apine_users` WHERE `email` = '$user_mail'  AND `type` <> 10");
		
		if ($id_sql) {
			return true;
		}
		
		return false;
	
	}
	/**
	 * Fetch all users
	 * @return ApineCollection
	 */
	public static function create_all () {

		/*$database = new ApineDatabase();
		$request = $database->select('SELECT `id` from `apine_users` ORDER BY `username` AND `type`<>10');*/
		$request = (new Apine\Core\Database())->select('SELECT `id` from `apine_users` ORDER BY `username` AND `type` <> 10');
		$liste = new Apine\Core\Collection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$class = self::get_user_class();
				$liste->add_item(new $class((int) $item['id']));
			}
		}
		
		return $liste;
	
	}

	/**
	 * Fetch a user by id
	 * 
	 * @param integer $a_id
	 *        User Identifier
	 * @return ApineUser
	 */
	public static function create_by_id ($a_id) {
		
		$database=new Apine\Core\Database();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_users` WHERE `id` = ? AND `type` <> 10');
		$ar_user_sql = $database->execute(array(
						$a_id
		), $user_sql_id);
		
		if ($ar_user_sql) {
			$class = self::get_user_class();
			$return = new $class((int) $ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
		
		return $return;
	
	}

	/**
	 * Fetch a user by username
	 * 
	 * @param string $name
	 *        User username
	 * @return ApineUser
	 */
	public static function create_by_name ($name) {

		$database = new Apine\Core\Database();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_users` WHERE `username` = ? OR `email` = ?  AND `type` <> 10');
		$ar_user_sql = $database->execute(array(
						$name,
						$name
		), $user_sql_id);
		
		if ($ar_user_sql) {
			$class = self::get_user_class();
			$return = new $class((int) $ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
		
		return $return;
	
	}

	/**
	 * Fetch users by permission level
	 * 
	 * @param integer $access
	 *        User Permission level
	 * @return ApineCollection
	 */
	public static function create_by_access_right ($access) {

		//$request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select("SELECT `id` FROM `apine_users` WHERE `type`=$access");
		$liste = new Apine\Core\Collection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$class = self::get_user_class();
				$liste->add_item(new $class((int) $item['id']));
			}
		}
		
		return $liste;
	
	}
	
	/**
	 * Fetch users by group id
	 * 
	 * @param integer $group
	 *        User Group id
	 * @return ApineCollection
	 */
	public static function create_by_group ($group) {
	
		//$request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select("SELECT `user_id` FROM `apine_users_user_groups` WHERE `group_id`=$group");
		$liste = new Apine\Core\Collection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$class = self::get_user_class();
				$liste->add_item(new $class((int) $item['user_id']));
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
	public static function authentication ($name, $pass) {
		
		$database = new Apine\Core\Database();
		$connect_sql_id = $database->prepare('SELECT `id` FROM `apine_users` WHERE ( `username`=? OR `email`=? ) AND `password`=? AND `type`<>10');
		$ar_connect_sql = $database->execute(array(
						$name,
						$name,
						$pass
		), $connect_sql_id);
		
		if ($ar_connect_sql) {
			$connect = end($ar_connect_sql);
			$connect = $connect['id'];
		} else {
			$connect = 0; // Value of false
		}
		
		return $connect;
	
	}
	
	/**
	 * Get name of the user class to use
	 * 
	 * @return string
	 */
	public static function get_user_class () {
		
		static $class;
	
		if (is_null($class)) {
			if (Apine\Application\ApplicationConfig::get('runtime', 'user_class')) {
				$pos_slash = strpos(Apine\Application\ApplicationConfig::get('runtime', 'user_class'), '/');
				$class = substr(Apine\Application\ApplicationConfig::get('runtime', 'user_class'), $pos_slash+1);
				
				/*if (!class_exists($class) || !is_subclass_of($class, 'ApineUser')) {
					$class = 'ApineUser';
				}*/
				if (!is_a($class, 'Apine\User\User', true)) {
					$class = 'Apine\User\User';
				}
			} else {
				$class = "Apine\User\User";
			}
		}
		
		return $class;
		
	}

}