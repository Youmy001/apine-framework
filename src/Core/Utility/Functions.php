<?php
/**
 * Utility Functions
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Utility;

use Apine\Core\Utility\StringManipulation;
use Apine\Core\Utility\Types;
use Apine\Core\Utility\Files;
use Apine\Core\Utility\Math;

/**
 * A split method that supports unicode characters
 *
 * @param string  $str
 * @param integer $l
 *
 * @return string
 * @see StringManipulation::splitUnicode()
 */
function strSplitUnicode($str, $l = 0)
{
    
    return StringManipulation::splitUnicode($str, $l);
    
}

/**
 * Check if a string is a valid ISO 8601 Timestamp
 *
 * @param string $timestamp
 *
 * @return boolean
 * @see Types::isTimestamp()
 */
function isTimestamp($timestamp)
{
    
    return Types::isTimestamp($timestamp);
    
}


/**
 * Check if a string is a valid JSON string
 *
 * @param string $string
 *
 * @return boolean
 * @see Types::isJson()
 */
function isJson($string)
{
    
    return Types::isJson($string);
    
}

/**
 * Compute a ratio from a multiplier
 *
 * @param double $n
 *        Ratio multiplier
 * @param float  $tolerance
 *        Precision level of the procedure
 *
 * @return string
 * @see Math::floatToRatio()
 */
function float2rat($n, $tolerance = 1.e-6)
{
    
    return Math::floatToRatio($n, $tolerance);
    
}

/**
 * Verify if exec is disabled
 *
 * @see Files::isExecAvailable()
 */
function isExecAvailable()
{
    return Files::isExecAvailable();
}

/**
 * Recursive file copy
 *
 * @param string $src
 *            Source directory
 * @param string $dst
 *            Destination directory
 *
 * @see Files::recursiveCopy()
 */
function recurseCopy($src, $dst)
{
    Files::recursiveCopy($src, $dst);
}

/**
 * Return the extension from a file name
 *
 * @param string $a_file_path
 *
 * @return string
 * @see Files::fileExtension()
 */
function fileExtension($a_file_path)
{
    return Files::fileExtension($a_file_path);
}

/**
 * @param mixed   $var
 * @param string  $function
 * @param boolean $negate
 *
 * @return boolean
 * @see Types::isReference()
 */
function isReference(&$var, $function = '', $negate = false)
{
    return Types::isReference($var, $function, $negate);
}

/**
 * Return the execution time
 * @return string
 */
function executionTime()
{
    static $before;
    $return = '';
    
    if (is_null($before)) {
        $before = microtime(true) * 1000;
    } else {
        $after = microtime(true) * 1000;
        $time = number_format($after - $before, 1);
        
        $return = $time;
    }
    
    return $return;
}