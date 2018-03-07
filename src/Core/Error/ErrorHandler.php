<?php
/**
 * ErrorHandler
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


namespace Apine\Core\Error;


/**
 * Class ErrorHandler
 *
 * Error handler that converts PHP errors to exceptions
 * and handles every exceptions launched from within
 * the application.
 *
 * @package Apine\Core\Error
 */
class ErrorHandler
{
    public static function handleError(int $errorNumber, string $errorString = null, string $errorFile = null, int $errorLine = null) : bool
    {
        $exception = new \RuntimeException($errorString, $errorNumber);
        //$exception->file = $errorFile;
        //$exception->line = $errorLine;
        
        throw $exception;
    }
    
    public static function handleException(\Throwable $e)
    {
        // TODO Add manipulation to print out error
        print $e->getMessage();
        print '<br/>';
        print $e->getTraceAsString();
    }
    
    public static function set()
    {
        error_reporting(E_ALL);
        set_error_handler([self::class, 'handleError'], E_ALL);
        set_exception_handler([self::class, 'handleException']);
    }
    
    public static function unset()
    {
        error_reporting(ini_get('error_reporting'));
        restore_error_handler();
        restore_exception_handler();
    }
}