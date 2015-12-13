<?php
/**
 * Factory for password tokens
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

class ApinePasswordTokenFactory implements ApineEntityFactory {
	
	/**
	 * Verify if the identifier exists
	 * 
	 * @param integer $user_id
	 * @return boolean
	 */
	public static function is_id_exist ($a_user_id) {
	
		// $id_sql = (new Database())
		$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_password_tokens` WHERE `id` = $a_user_id");
	
		if ($id_sql) {
			return true;
		}
	
		return false;
	
	}
	
	/**
	 * Verify if the token string exists
	 * 
	 * @param string $a_token
	 * @return boolean
	 */
	public static function is_token_exist ($a_token) {
	
		// $id_sql = (new Database())
		$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_password_tokens` WHERE `token` = '$a_token'");
	
		if ($id_sql) {
			return true;
		}
	
		return false;
	
	}
	
	/**
	 * Verify if the token string is valid
	 * 
	 * @param string $a_token
	 * @return boolean
	 */
	public static function is_token_valid ($a_token) {
		
		$expiration_gap = date("Y-m-d H:i:s", strtotime("24 hours ago"));
		$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_password_tokens` WHERE `token` = '$a_token' AND `creation_date` > '$expiration_gap'");
		
		if ($id_sql) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Fetch all password tokens
	 * 
	 * @return ApineCollection
	 */
	public static function create_all () {
	
		// $request = (new Database())
		$database = new ApineDatabase();
		$request = $database->select('SELECT `id` from `apine_password_tokens` ORDER BY `user_id` ASC');
		$liste = new ApineCollection();
	
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new ApinePasswordToken($item['id']));
			}
		}
	
		return $liste;
	
	}
	
	/**
	 * Fetch a password token by id
	 * 
	 * @param integer $a_id
	 * @return ApinePasswordToken
	 */
	public static function create_by_id ($a_id) {
	
		$database = new ApineDatabase();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_password_tokens` WHERE `id` = ?');
		$ar_user_sql = $database->execute(array(
						$a_id
		), $user_sql_id);
	
		if ($ar_user_sql) {
			$return = new ApinePasswordToken($ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
	
		return $return;
	
	}
	
	/**
	 * Fetch a password token by token string
	 * 
	 * @param string $a_token
	 * @return ApinePasswordToken
	 */
	public static function create_by_token ($a_token) {
	
		$database = new ApineDatabase();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_password_tokens` WHERE `token` = ?');
		$ar_user_sql = $database->execute(array(
						$a_token
		), $user_sql_id);
	
		if ($ar_user_sql) {
			$return = new ApinePasswordToken($ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
	
		return $return;
	
	}
	
}