<?php
/**
 * Cookie Access tool
 * This script contains an helper to read and write cookies
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core;

/**
 * Cookie writing and reading tool
 * Tool to easily read and write cookies
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Cookie
{
    /**
     * Get cookie by name
     *
     * @param string $cookie_name
     *
     * @throws \ErrorException
     * @return string
     */
    public static function get(string $cookie_name)
    {
        if (isset($_COOKIE[$cookie_name])) {
            return $_COOKIE[$cookie_name];
        } else {
            throw new \ErrorException();
        }
    }
    
    /**
     * Set a new cookie value
     *
     * @param string  $cookie_name
     * @param string  $value
     * @param integer $expiration_time
     *        Expiration date in miliseconds
     *
     * @return boolean
     */
    public static function set(string $cookie_name, string $value, int $expiration_time = 0) : bool
    {
        if ($expiration_time == 0) {
            return setcookie($cookie_name, $value, null, '/');
        } else {
            return setcookie($cookie_name, $value, $expiration_time, '/');
        }
    }
}
