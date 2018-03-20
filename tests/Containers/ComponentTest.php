<?php
/**
 * ComponentTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Container\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    public function testConstructor()
    {
        $object = new class(){};
        $component = new Component('component', $object, false);
        $this->assertAttributeEquals('component', 'name', $component);
        $this->assertAttributeEquals($object, 'content', $component);
        $this->assertAttributeEquals(false, 'factory', $component);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage A factory must be a callable
     */
    public function testConstructorFactoryContentMustBeACallable()
    {
        $object = new TestClass();
        $component = new Component('component', $object, true);
    }
    
    public function testIsFactory()
    {
        $object = new class(){};
        $component = new Component('component', function () use ($object) {
            return new $object;
        }, true);
        
        $this->assertEquals(true, $component->isFactory());
    }
    
    public function testGetName()
    {
        $object = new class(){};
        $component = new Component('component', function () use ($object) {
            return new $object;
        }, true);
    
        $this->assertEquals('component', $component->getName());
    }
    
    public function testInvoke()
    {
        $component = new Component('component', 'text_content');
        $value = $component->invoke();
        $this->assertAttributeNotEmpty('computed', $component);
        $this->assertEquals('text_content', $value);
    }
    
    public function testInvokeOnFactory()
    {
        $component = new Component('component', function () {
            return new TestClass();
        }, true);
        $value = $component->invoke();
        $this->assertAttributeEmpty('computed', $component);
        $this->assertInstanceOf(TestClass::class, $value);
    }
    
    public function testHasTypeOnStringContent()
    {
        $component = new Component('component', 'text content', false);
        
        $this->assertEquals(true, $component->hasType('string'));
    }
    
    public function testHasTypeOnObjectContent()
    {
        $object = new TestClass();
        $component = new Component('component', $object, false);
        
        $this->assertEquals(true, $component->hasType(TestClass::class));
    }
    
    public function testHasTypeOnCallableContent()
    {
        $component = new Component('component', function () {
            return new TestClass();
        }, false);
        
        $this->assertEquals(true, $component->hasType(TestClass::class));
    }
    
    public function testHasTypeOnFactoryContent()
    {
        $component = new Component('component', function () {
            return new TestClass();
        }, true);
        
        $this->assertEquals(true, $component->hasType(TestClass::class));
    }
}

class TestClass{};