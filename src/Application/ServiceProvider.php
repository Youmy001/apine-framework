<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 07/01/18
 * Time: 11:14 PM
 */

namespace Apine\Application;

use Apine\Core\Container\Container;
use Apine\Core\Http\Request;

final class ServiceProvider extends Container
{
    /**
     * @var ServiceProvider
     */
    private static $instance;
    
    private function __construct()
    {
        //$this->registerDefaultServices();
    }
    
    public static function getInstance() : ServiceProvider
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
    
        return self::$instance;
    }
    
    public function registerDefaultServices() : void
    {
        $this->register(Request::class, function() : void {
            $request = Request::createFromGlobals();
        });
    }
}