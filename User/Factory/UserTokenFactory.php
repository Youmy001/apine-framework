<?php
/**
 * Factory for api user tokens
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\User\Factory;

use Apine;

class UserTokenFactory implements Apine\Entity\EntityFactoryInterface {

	/**
	 * Verify if the identifier exists
	 * 
	 * @param integer $user_id        
	 * @return boolean
	 */
	public static function is_id_exist ($a_user_id) {
		
		// $id_sql = (new Database())
		$database = new Apine\Core\Database();
		$id_sql = $database->select("SELECT `id` FROM `apine_api_users_tokens` WHERE `id` = $a_user_id");
		
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
		$database = new Apine\Core\Database();
		$id_sql = $database->select("SELECT `id` FROM `apine_api_users_tokens` WHERE `token` = '$a_token'");
	
		if ($id_sql) {
			return true;
		}
	
		return false;
	
	}

	/**
	 * Fetch all api user tokens
	 * 
	 * @return ApineCollection
	 */
	public static function create_all () {
		
		// $request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select('SELECT `id` from `apine_api_users_tokens` ORDER BY `user_id` ASC');
		$liste = new ApineCollection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new Apine\User\UserToken((int) $item['id']));
			}
		}
		
		return $liste;
	
	}

	/**
	 * Fetch a api user token by id
	 * 
	 * @param integer $a_id
	 * @return ApineUserToken
	 */
	public static function create_by_id ($a_id) {
		
		$database = new Apine\Core\Database();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_api_users_tokens` WHERE `id` = ?');
		$ar_user_sql = $database->execute(array(
						$a_id
		), $user_sql_id);
		
		if ($ar_user_sql) {
			$return = new Apine\User\UserToken((int) $ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
		
		return $return;
	
	}
	
	/**
	 * Fetch a api user token by token string
	 *
	 * @param string $a_token
	 * @return ApineUserToken
	 */
	public static function create_by_token ($a_token) {
		
		$database = new ApineDatabase();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_api_users_tokens` WHERE `token` = ?');
		$ar_user_sql = $database->execute(array(
						$a_token
		), $user_sql_id);
		
		if ($ar_user_sql) {
			$return = new ApineUserToken((int) $ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
		
		return $return;
		
	}

	/**
	 * Authentifiate a user with a combination of a user name and a
	 * token string.
	 *
	 * @param string $name
	 *        Username
	 * @param string $a_token
	 *        Token string
	 * @return boolean
	 */
	public static function authentication ($a_name, $a_token, $a_delay) {
		
		$user = ApineUserFactory::create_by_name($a_name);

		$database = new ApineDatabase();
		$token_statement_id = $database->prepare('SELECT `id` FROM `apine_api_users_tokens` WHERE `user_id` = ? AND `token` = ? AND `last_access_date` > ? AND `disabled` = false');
		$ar_token = $database->execute(array(
						$user->get_id(),
						$a_token,
						date('d M Y H:i:s',time() - $a_delay)
		), $token_statement_id);
		
		if ($ar_token) {
			$connect = end($ar_token);
			$return = (int) $connect['id'];
		} else {
			$return = false;
		}
		
		return $return;
	
	}

}