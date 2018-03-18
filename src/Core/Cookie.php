<?php
/**
 * Cookie Access tool
 * This script contains an helper to read and write cookies
 *
 * @license MIT
 * @copyright 2015-18 Tommy Teasdale
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
     * @param string $cookie
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function get(string $cookie) : string
    {
        if (isset($_COOKIE[$cookie])) {
            return $_COOKIE[$cookie];
        } else {
            throw new \InvalidArgumentException(sprintf('Cookie %s not found', $cookie));
        }
    }
    
    /**
     * Set a new cookie value
     *
     * @param string  $cookie_name
     * @param string  $value
     * @param integer $expiration_time
     *        Expiration date in milliseconds
     *
     * @return boolean
     */
    public static function set(string $cookie_name, string $value, int $expiration_time = 0) : bool
    {
        return setcookie($cookie_name, $value, $expiration_time, '/');
    }
}
