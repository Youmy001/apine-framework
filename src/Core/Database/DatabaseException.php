<?php
/**
 * Custom Exception Handler
 * This script contains a custom exception handler
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

namespace Apine\Core\Error;

/**
 * Custom implementation of the PDO exception handler
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core\Error
 */
class DatabaseException extends \PDOException
{
}