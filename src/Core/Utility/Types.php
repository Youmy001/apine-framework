<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/25
 * Time: 14:46
 */

namespace Apine\Core\Utility;


class Types
{
    /**
     * Check if a string is a valid ISO 8601 Timestamp
     * Source : http://community.sitepoint.com/t/check-whether-the-string-is-timestamp/4468/19
     *
     * @param string $a_timestamp
     *
     * @return boolean
     */
    public static function isTimestamp($a_timestamp)
    {
        return (bool)preg_match('/^(?:(?P<year>[-+]\\d{4,}|\\d{4})(?:(?:-(?P<month>1[012]|0[1-9])(?:-(?P<day>3[01]|[12]\\d|0[1-9]))?)|(?:-[Ww](?P<yearweek>5[0-3]|[1-4]\\d|0[1-9])(?:-(?P<weekday>[1-7]))?)|(?:-(?P<yeardays>36[0-6]|3[0-5]\\d|[12]\\d{2}|0[1-9]\\d|00[1-9])))?)(?:(?:[Tt]| +)(?P<hour>2[0-4]|[01]\\d)(?:\\:(?P<minutes>[0-5]\\d)(?:\\:(?P<seconds>60|[0-5]\\d))?)?(?P<fraction>[,.][\\d.]+)?\\s*(?P<timezone>Z|[+-](?:1[0-4]|0[0-9])(?:\\:?[0-5]\\d)?)?)?$/',
            $a_timestamp);
    }
    
    /**
     * Check if a string is a valid JSON string
     *
     * @param string $a_string
     *
     * @return boolean
     */
    public static function isJson($a_string)
    {
        json_decode($a_string);
        
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    /**
     * @param mixed   $a_var
     * @param string  $a_function
     * @param boolean $a_negate
     *
     * @return boolean
     */
    public static function isReference(&$a_var, $a_function = '', $a_negate = false)
    {
        $stat = true;
        
        if (!isset($a_var)) {
            $stat = false;
        } else {
            if (!empty($a_function) && function_exists($a_function)) {
                $stat = $a_function($a_var);
                $stat = ($a_negate) ? $stat ^ 1 : $stat;
            } else {
                if ($a_function === 'empty') {
                    $stat = empty($a_var);
                    $stat = ($a_negate) ? $stat ^ 1 : $stat;
                } else {
                    if (!function_exists($a_function)) {
                        $stat = false;
                        trigger_error("$a_function() is not a valid function");
                    }
                }
            }
            
            $stat = ($stat) ? true : false;
        }
        
        return $stat;
    }
    
    /**
     * Return the calling class to the class using it
     *
     * @return string|null Calling Class
     * @author Kyle Farris (https://github.com/kylefarris), hasmstar (https://github.com/hamstar)
     * @see https://gist.github.com/kylefarris/5188645
     */
    /*public static function get_calling_class() {
        
        //get the trace
        $trace = debug_backtrace();
        
        // Get the class that is asking for who awoke it
        $class = ( isset( $trace[1]['class'] ) ? $trace[1]['class'] : NULL );
        
        // +1 to i cos we have to account for calling this function
        for ( $i=1; $i<count( $trace ); $i++ ) {
            if ( isset( $trace[$i] ) && isset( $trace[$i]['class'] ) ) // is it set?
                if ( $class != $trace[$i]['class'] ) // is it a different class
                    return $trace[$i]['class'];
        }
        
    }*/
    public static function getCallingClass()
    {
        // Get the class trace
        $trace = debug_backtrace();
        
        // Get the class that is asking for who awoke it
        // This method can be call directly or through
        // an helper function
        if (isset($trace[1]['class'])) {
            $class = $trace[1]['class'];
            $start = 1;
        } else {
            if (isset($trace[2]['class'])) {
                $class = $trace[2]['class'];
                $start = 2;
            } else {
                $class = null;
                $start = 1;
            }
        }
        
        // +1 to i cos we have to account for calling this function
        for ($i = $start; $i < count($trace); $i++) {
            if (isset($trace[$i]) && isset($trace[$i]['class'])) // is it set?
            {
                if ($class != $trace[$i]['class']) // is it a different class
                {
                    return $trace[$i]['class'];
                }
            }
        }
        
        return null;
    }
}