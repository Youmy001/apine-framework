<?php

class ApineUserTokenFactory extends ApineEntityFactory {

	/**
	 * Verify if the identifier exists
	 * @param integer $user_id        
	 * @return boolean
	 */
	public static function is_id_exist ($a_user_id) {
		
		// $id_sql = (new Database())
		$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_api_users_tokens` WHERE `id`=$a_user_id");
		
		if ($id_sql) {
			return true;
		}
		
		return false;
	
	}
	
	public static function is_token_exist ($a_token) {
	
		// $id_sql = (new Database())
		$database = new ApineDatabase();
		$id_sql = $database->select("SELECT `id` FROM `apine_api_users_tokens` WHERE `token`=$a_token");
	
		if ($id_sql) {
			return true;
		}
	
		return false;
	
	}

	public static function create_all () {
		
		// $request = (new Database())
		$database = new ApineDatabase();
		$request = $database->select('SELECT `id` from `apine_api_users_tokens` ORDER BY `user_id` ASC');
		$liste = new ApineCollection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new ApineUserToken($item['id']));
			}
		}
		
		return $liste;
	
	}

	public static function create_by_id ($a_id) {

		$database = new ApineDatabase();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_api_users_tokens` WHERE `id`=?');
		$ar_user_sql = $database->execute(array(
						$a_id
		), $user_sql_id);
		
		if ($ar_user_sql) {
			$return = new ApineUserToken($ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
		
		return $return;
	
	}
	
	public static function create_by_token ($a_token) {
		
		$database = new ApineDatabase();
		$user_sql_id = $database->prepare('SELECT `id` FROM `apine_api_users_tokens` WHERE `token`=?');
		$ar_user_sql = $database->execute(array(
						$a_token
		), $user_sql_id);
		
		if ($ar_user_sql) {
			$return = new ApineUserToken($ar_user_sql[0]['id']);
		} else {
			$return = null;
		}
		
		return $return;
		
	}

	public static function authentication ($a_name, $a_token, $a_delay) {
		
		$user = ApineUserFactory::create_by_name($a_name);

		$database = new ApineDatabase();
		$token_statement_id = $database->prepare('SELECT `id` FROM `apine_api_users_tokens` WHERE `id_user` = ? AND `token` = ? AND `last_access_date` > ? AND `disabled` = false');
		$ar_token = $database->execute(array(
						$user->get_id(),
						$a_token,
						date('d M Y H:i:s',time() - $a_delay)
		), $token_statement_id);
		
		if ($ar_token) {
			$connect = end($ar_token);
			$return = $connect['id'];
		} else {
			$return = false;
		}
		
		return $connect;
	
	}

}