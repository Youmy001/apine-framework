<?php
/**
 * Custom Exception Handler
 * This script contains a custom exception handler
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Exception;

/**
 * Custom implementation of the PDO exception handler
 *
 * @author Tommy Teasdale
 */
class DatabaseException extends \PDOException {
}