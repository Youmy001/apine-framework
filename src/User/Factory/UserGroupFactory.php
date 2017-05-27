<?php
/**
 * User Group Factory Declaration
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\User\Factory;

use Apine;
use Apine\Core\Collection;
use Apine\Core\Database;
use Apine\User\UserGroup;

/**
 * Class UserGroupFactory
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User\Factory
 */
class UserGroupFactory implements Apine\Entity\EntityFactoryInterface
{
    /**
     * Verify if the identifier exists
     *
     * @param integer $a_id
     *
     * @return boolean
     */
    public static function isIdExist($a_id)
    {
        $database = new Database();
        $request = $database->select("SELECT `id` FROM `apine_user_groups` WHERE `id`=$a_id");
        
        if ($request != null && count($request) > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Fetch all user groups
     *
     * @return Collection[UserGroup]
     */
    public static function createAll()
    {
        $database = new Database();
        $request = $database->select("SELECT `id` FROM `apine_user_groups` ORDER BY `id`");
        $liste = new Collection();
        
        if ($request != null && count($request) > 0) {
            foreach ($request as $item) {
                $liste->addItem(new UserGroup((int)$item['id']));
            }
        }
        
        return $liste;
    }
    
    /**
     * Fetch a user group by id
     *
     * @param integer $a_id
     *
     * @return UserGroup
     */
    public static function createById($a_id)
    {
        $database = new Database();
        $request = $database->select("SELECT `id` FROM `apine_user_groups` WHERE `id`=$a_id");
        
        if ($request != null && count($request) > 0) {
            $return = new UserGroup((int)$request[0]['id']);
        } else {
            $return = null;
        }
        
        return $return;
    }
    
    /**
     * Fetch groups by user
     *
     * @param integer $user
     *        User id
     *
     * @return Collection[UserGroup]
     */
    public static function createByUser($user)
    {
        $database = new Database();
        $request = $database->select("SELECT `group_id` FROM `apine_users_user_groups` WHERE `user_id`=$user");
        $liste = new Collection();
        
        if ($request != null && count($request) > 0) {
            foreach ($request as $item) {
                $liste->add_item(new Apine\User\UserGroup((int)$item['group_id']));
            }
        }
        
        return $liste;
    }
}