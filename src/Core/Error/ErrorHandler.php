<?php
/**
 * ErrorHandler
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Error;

use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Apine\Exception\GenericException;


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
    /**
     * @var int
     */
    public static $reportingLevel = 0;
    
    /**
     * @param int         $errorNumber
     * @param string      $errorString
     * @param string|null $errorFile
     * @param int|null    $errorLine
     *
     * @throws \RuntimeException
     */
    public static function handleError(int $errorNumber, string $errorString = '', string $errorFile = null, int $errorLine = null) : void
    {
        $exception = new \RuntimeException($errorString, $errorNumber);
        //$exception->file = $errorFile;
        //$exception->line = $errorLine;
        
        throw $exception;
    }
    
    /**
     * @param \Throwable $e
     */
    public static function handleException(\Throwable $e) : void
    {
        $response = new Response(500);
        $response = $response->withAddedHeader('Content-Type', 'text/plain');
        
        if ($e instanceof GenericException) {
            $response = $response->withStatus($e->getCode());
        }
    
        $result = $e->getMessage() . "\n\r";
        
        if (self::$reportingLevel === 1) {
            $trace = explode("\n", $e->getTraceAsString());
    
            foreach ($trace as $step) {
                $result .= "\n";
                $result .= $step;
            }
        }
    
        $content = new Stream(fopen('php://memory', 'r+'));
        $content->write($result);
        
        $response = $response->withBody($content);
        
        /* Send Headers */
        if (!headers_sent()) {
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
            
            foreach ($response->getHeaders() as $name => $values) {
                if (is_array($values)) {
                    $values = implode(", ", $values);
                }
    
                header(sprintf('%s: %s', $name, $values), false);
            }
        }
        
        // Then send body
        $body = $response->getBody();
        
        if ($body->isSeekable()) {
            $body->rewind();
        }
        
        print $body->getContents();
    }
    
    /**
     * @param int $reportLevel
     */
    public static function set(int $reportLevel = 0) : void
    {
        self::unset();
        self::$reportingLevel = $reportLevel;
        
        error_reporting(E_ALL);
        set_error_handler([self::class, 'handleError'], E_ALL);
        set_exception_handler([self::class, 'handleException']);
    }
    
    
    public static function unset() : void
    {
        error_reporting((int) ini_get('error_reporting'));
        restore_error_handler();
        restore_exception_handler();
    }
}