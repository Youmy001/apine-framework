<?php
/**
 * User Group Factory Declaration
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\User\Factory;

use Apine;

class UserGroupFactory implements Apine\Entity\EntityFactoryInterface {

	/**
	 * Verify if the identifier exists
	 *
	 * @param integer $user_id
	 * @return boolean
	 */
	public static function is_id_exist ($a_id) {

		//$request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select("SELECT `id` FROM `apine_user_groups` WHERE `id`=$a_id");
		
		if ($request != null && count($request) > 0) {
			return true;
		}
		
		return false;

	}

	/**
	 * Fetch all user groups
	 * 
	 * @return ApineCollection
	 */
	public static function create_all () {

		//$request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select("SELECT `id` FROM `apine_user_groups` ORDER BY `id`");
		$liste = new Apine\Core\Collection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new Apine\User\UserGroup((int) $item['id']));
			}
		}
		
		return $liste;

	}

	/**
	 * Fetch a user group by id
	 * 
	 * @param integer $a_id
	 * @return ApineUserGroup
	 */
	public static function create_by_id ($a_id) {

		//$request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select("SELECT `id` FROM `apine_user_groups` WHERE `id`=$a_id");
		
		if ($request != null && count($request) > 0) {
			$return = new Apine\User\UserGroup((int) $request[0]['id']);
		}else{
			$return = null;
		}
		
		return $return;

	}
	
	/**
	 * Fetch apine_user_groups by user
	 * 
	 * @param integer $user
	 *        User id
	 * @return ApineCollection
	 */
	public static function create_by_user ($user) {
	
		//$request = (new Database())
		$database = new Apine\Core\Database();
		$request = $database->select("SELECT `group_id` FROM `apine_users_user_groups` WHERE `user_id`=$user");
		$liste = new Apine\Core\Collection();
		
		if ($request != null && count($request) > 0) {
			foreach ($request as $item) {
				$liste->add_item(new Apine\User\UserGroup((int) $item['group_id']));
			}
		}
		
		return $liste;
	
	}

}