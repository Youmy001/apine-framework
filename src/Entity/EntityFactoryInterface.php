<?php
/**
 * Basic Factory declaration.
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Entity;

/**
 * This is the implementation of the factory
 * design patern.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Entity
 */
interface EntityFactoryInterface
{
    /**
     * Procedure to fetch every rows in a factory's scope
     *
     * @static
     */
    public static function createAll();
    
    /**
     * Procedure to fetch a row in a factory's scope matching provided
     * identifier
     *
     * @param string $a_id
     *        Identifier of the row to fetch
     *
     * @static
     */
    public static function createById($a_id);
}