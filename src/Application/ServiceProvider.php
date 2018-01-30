<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 07/01/18
 * Time: 11:14 PM
 */

namespace Apine\Application;

use Apine\Core\Container\Container;

final class ServiceProvider extends Container
{
    private static $instance;
    
    private function __construct()
    {
        //$this->registerDefaultServices();
    }
    
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
    
        return self::$instance;
    }
}