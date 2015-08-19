<?php

class ApineUserGroupFactory extends ApineFactory{

	public static function is_id_exist($a_id) {

		//$request = (new Database())
		$database=new Database();
		$request = $request->select("SELECT `id` FROM `apine_user_groups` WHERE `id`=$a_id");
		
		if ($request != null && count($request) > 0) {
			return true;
		}
		
		return false;

	}

	public static function create_all() {

		//$request = (new Database())
		$database=new Database();
		$request = $request->select("SELECT `id` FROM `apine_user_groups` ORDER BY `id`");
		$liste = new Liste();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new ApineUserGroup($item['id']));
			}
		}
		
		return $liste;

	}

	public static function create_by_id($a_id) {

		//$request = (new Database())
		$database=new Database();
		$request = $request->select("SELECT `id` FROM `apine_user_groups` WHERE `id`=$a_id");
		
		if ($request != null && count($request) > 0) {
			$return = new ApineUserGroup($request[0]['id']);
		}else{
			$return = null;
		}
		
		return $return;

	}
	
	/**
	 * Fetch apine_user_groups by user
	 * @param integer $user
	 *        ApineUser id
	 * @return Liste
	 */
	public static function create_by_user($user) {
	
		//$request = (new Database())
		$database=new Database();
		$request = $request->select("SELECT `group_id` FROM `apine_users_user_groups` WHERE `user_id`=$user");
		$liste = new Liste();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new ApineUserGroup($item['group_id']));
			}
		}
		
		return $liste;
	
	}

}