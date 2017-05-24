<?php
/**
 * Return the execution time
 * @return string
 */
function apine_execution_time()
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