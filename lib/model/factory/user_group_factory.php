<?php

class ApineUserGroupFactory extends ApineFactory implements ApineFactoryInterface{

	public static function is_id_exist($a_id){

		$request = self::_get_connection()->select("SELECT id FROM apine_user_groups WHERE id=$a_id");
		if($request != null && count($request) > 0){
			return true;
		}
		return false;

	}

	public static function create_all(){

		$request = self::_get_connection()->select("SELECT id FROM apine_user_groups ORDER BY id");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new Group($item['id']));
			}
		}
		return $liste;

	}

	public static function create_by_id($a_id){

		$request = self::_get_connection()->select("SELECT id FROM apine_user_groups WHERE id=$a_id");
		if($request != null && count($request) > 0){
			$return = new Group($request[0]['id']);
		}else{
			$return = null;
		}
		return $return;

	}

}