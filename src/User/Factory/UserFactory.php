<?php
/**
 * User Factory declaration.
 * This file contains the user factory class.
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\User\Factory;

use Apine;
use Apine\Application\Application;
use Apine\User\User;

/**
 * Class UserFactory
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User\Factory
 */
class UserFactory implements Apine\Entity\EntityFactoryInterface
{
    /**
     * Verify if the identifier exists
     *
     * @param integer $user_id
     *
     * @return boolean
     */
    public static function isIdExist($user_id)
    {
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
     *
     * @return boolean
     */
    public static function isNameExist($user_name)
    {
        $id_sql = (new Apine\Core\Database())->select("SELECT `id` FROM `apine_users` WHERE (`username`='$user_name' OR `email`='$user_name') AND `type`<>10;");
        
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
     *
     * @return boolean
     */
    public static function isEmailExist($user_mail)
    {
        $id_sql = (new Apine\Core\Database())->select("SELECT `id` FROM `apine_users` WHERE `email` = '$user_mail'  AND `type` <> 10");
        
        if ($id_sql) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Fetch all users
     *
     * @return Apine\Core\Collection
     */
    public static function createAll()
    {
        $request = (new Apine\Core\Database())->select('SELECT `id` from `apine_users` ORDER BY `username` AND `type` <> 10');
        $liste = new Apine\Core\Collection();
        
        if ($request != null && count($request) > 0) {
            foreach ($request as $item) {
                $class = self::getUserClass();
                $liste->addItem(new $class((int)$item['id']));
            }
        }
        
        return $liste;
    }
    
    /**
     * Fetch a user by id
     *
     * @param integer $a_id
     *        User Identifier
     *
     * @return Apine\User\User
     */
    public static function createById($a_id)
    {
        $database = new Apine\Core\Database();
        $user_sql_id = $database->prepare('SELECT `id` FROM `apine_users` WHERE `id` = ? AND `type` <> 10');
        $ar_user_sql = $database->execute(array(
            $a_id
        ), $user_sql_id);
        
        if ($ar_user_sql) {
            $class = self::getUserClass();
            $return = new $class((int)$ar_user_sql[0]['id']);
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
     *
     * @return Apine\User\User
     */
    public static function createByName($name)
    {
        $database = new Apine\Core\Database();
        $user_sql_id = $database->prepare('SELECT `id` FROM `apine_users` WHERE `username` = ? OR `email` = ?  AND `type` <> 10');
        $ar_user_sql = $database->execute(array(
            $name,
            $name
        ), $user_sql_id);
        
        if ($ar_user_sql) {
            $class = self::getUserClass();
            $return = new $class((int)$ar_user_sql[0]['id']);
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
     *
     * @return Apine\Core\Collection
     */
    public static function createByAccessRight($access)
    {
        
        $database = new Apine\Core\Database();
        $request = $database->select("SELECT `id` FROM `apine_users` WHERE `type`=$access");
        $liste = new Apine\Core\Collection();
        
        if ($request != null && count($request) > 0) {
            foreach ($request as $item) {
                $class = self::getUserClass();
                $liste->addItem(new $class((int)$item['id']));
            }
        }
        
        return $liste;
        
    }
    
    /**
     * Fetch users by group id
     *
     * @param integer $group
     *        User Group id
     *
     * @return Apine\Core\Collection
     */
    public static function createByGroup($group)
    {
        $database = new Apine\Core\Database();
        $request = $database->select("SELECT `user_id` FROM `apine_users_user_groups` WHERE `group_id`=$group");
        $liste = new Apine\Core\Collection();
        
        if ($request != null && count($request) > 0) {
            foreach ($request as $item) {
                $class = self::getUserClass();
                $liste->addItem(new $class((int)$item['user_id']));
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
     *
     * @return integer
     */
    public static function authentication($name, $pass)
    {
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
    public static function getUserClass()
    {
        static $class;
        
        if (is_null($class)) {
            $config = Application::getInstance()->getConfig();
            
            if ($config->session->user_class && !empty($config->session->user_class)) {
                $user_class = $config->session->user_class;
                $pos_slash = strpos($user_class, '/');
                $class = substr($user_class, $pos_slash + 1);
                
                if (!is_a($user_class, 'Apine\User\User')) {
                    $class = 'Apine\User\User';
                }
            } else {
                $class = 'Apine\User\User';
            }
        }
        
        return $class;
    }
    
}