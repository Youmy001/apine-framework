<?php
/**
 * Image Factory declaration.
 *
 * This file contains the image factory class.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */

class ApineImageFactory extends ApineFactory implements ApineFactoryInterface{

	/**
	 * Verify if the identifier exists
	 * @param integer $image_id        
	 * @return boolean
	 */
	public static function is_id_exist($image_id){

		$id_sql = self::_get_connection()->select("SELECT ID FROM apine_images WHERE ID=$image_id");
		if($id_sql){
			return true;
		}
		return false;
	
	}

	/**
	 * Verify if the external identifier exists
	 * @param string $image_id        
	 * @return boolean
	 */
	public static function is_access_id_exist($image_id){

		$id_sql = self::_get_connection()->select("SELECT ID FROM apine_images WHERE access_id='$image_id'");
		if($id_sql){
			return true;
		}
		return false;
	
	}

	/**
	 * Verify if the folder is owned by the user
	 * @param integer $user_id        
	 */
	public static function is_user_folder($user_id){

	}

	/**
	 *
	 * @return Liste
	 */
	public static function create_all(){

		$request = self::_get_connection()->select('SELECT ID from `apine_images` ORDER BY `ID`');
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}

	/**
	 * Fetch all apine_images inside a folder
	 * @param string $a_folder
	 *        Image folder
	 * @return Liste
	 */
	public static function create_all_folder($a_folder){

		$request = self::_get_connection()->select("SELECT ID from `apine_images` where folder=$a_folder ORDER BY `ID`");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}

	/**
	 *
	 * @param integer $a_id
	 *        Image identifier
	 * @return Image
	 */
	public static function create_by_id($a_id){

		$sql_id = self::_get_connection()->prepare('SELECT ID FROM `apine_images` WHERE ID=?');
		$ar_sql = self::_get_connection()->execute(array(
						$a_id
		), $sql_id);
		if($ar_sql){
			$return = new Image($ar_sql[0]['ID']);
		}else{
			$return = null;
		}
		return $return;
	
	}

	/**
	 * Fetch an Image by an external identifier
	 * @param string $access_id
	 *        External Identifier
	 * @return Image
	 */
	public static function create_by_access_id($access_id){

		$sql_id = self::_get_connection()->prepare('SELECT ID FROM `apine_images` WHERE access_id=?');
		$ar_sql = self::_get_connection()->execute(array(
						$access_id
		), $sql_id);
		if($ar_sql){
			$item_id = end($ar_sql);
			$item_id = $item_id['ID'];
			$item = new ApineImage($item_id);
		}else{
			$item = null;
		}
		return $item;
	
	}

	/**
	 * Fetch all apine_images owned by a user
	 * @param integer $a_user_id
	 *        User identifier
	 * @return Liste
	 */
	public static function create_by_user($a_user_id){

		$request = self::_get_connection()->select("SELECT ID from `apine_images` where user_id=$a_user_id");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}

	/**
	 * Fetch all apine_images owned by a user in a folder
	 * @param integer $a_user_id
	 *        User identifier
	 * @param string $a_folder
	 *        Folder name
	 * @return Liste
	 */
	public static function create_by_user_folder($a_user_id, $a_folder){

		$request = self::_get_connection()->select("SELECT ID from `apine_images` where user_id=$a_user_id AND folder=$a_folder");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}

	/**
	 * Fetch all user's public apine_images
	 * @param intger $a_user_id
	 *        User identifier
	 * @return Liste
	 */
	public static function create_by_user_public($a_user_id){

		$request = self::_get_connection()->select("SELECT ID from `apine_images` where user_id=$a_user_id OR privacy=2");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}

	/**
	 * Fetch user's public apine_images in a folder
	 * @param integer $a_user_id
	 *        User identifier
	 * @param string $a_folder
	 *        Folder name
	 * @return Liste
	 */
	public static function create_by_user_folder_public($a_user_id, $a_folder){

		$request_id = self::_get_connection()->prepare("SELECT ID from `apine_images` where ( user_id=? AND folder=? ) OR ( privacy=2 AND folder=? )");
		$request = self::_get_connection()->execute(array(
						$a_user_id,
						$a_folder,
						$a_folder
		), $request_id);
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}
	
	/**
	 * Fetch all apine_images by privacy level
	 * @param integer $privacy Privacy level
	 * @return Liste
	 */
	public static function create_by_privacy($privacy){

		$request = self::_get_connection()->select("SELECT ID from `apine_images` where privacy=$privacy");
		$liste = new Liste();
		if($request != null && count($request) > 0){
			foreach($request as $item){
				$liste->add_item(new ApineImage($item['ID']));
			}
		}
		return $liste;
	
	}

}