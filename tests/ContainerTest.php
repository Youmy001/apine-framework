<?php
/**
 * ContainerTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Container\Container;
use Apine\Core\Container\ContainerException;
use Apine\Core\Container\ContainerNotFoundException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    
    public function testGet()
    {
        $container = new Container();
        $container->register(StubClass::class, function(){return new StubClass();});
        
        $this->assertInstanceOf(StubClass::class, $container->get(StubClass::class));
    }
    
    public function testGetNotFound()
    {
        $this->expectException(ContainerNotFoundException::class);
        
        $container = new Container();
        $container->register(StubClass::class, function(){return new StubClass();});
    
        $container->get(StubClassTwo::class);
    }
    
    public function testGetError()
    {
        $this->expectException(ContainerException::class);
    
        $container = new Container();
        $container->register(StubClass::class, function($str){return new $str();});
        $container->get(StubClass::class);
    }
    
    public function testHas()
    {
        $container = new Container();
        $container->register(StubClass::class, function(){return new StubClass();});
        
        $this->assertTrue($container->has(StubClass::class));
    }
}

class StubClass{}

class StubClassTwo{}