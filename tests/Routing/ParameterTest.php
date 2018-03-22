<?php
/**
 * ParameterTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Config;
use Apine\Core\Routing\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    
    public function testConstructor()
    {
        $parameter = new Parameter('string', 'value');
        $this->assertAttributeNotEmpty('type', $parameter);
        $this->assertAttributeNotEmpty('name', $parameter);
        $this->assertAttributeEquals('string', 'type', $parameter);
        $this->assertAttributeEquals('value', 'name', $parameter);
    }
    
    public function testIsBuiltInString()
    {
        $parameter = new Parameter('string', 'value');
        $this->assertEquals(true, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInInteger()
    {
        $parameter = new Parameter('int', 'value');
        $this->assertEquals(true, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInFloat()
    {
        $parameter = new Parameter('float', 'value');
        $this->assertEquals(true, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInBoolean()
    {
        $parameter = new Parameter('bool', 'value');
        $this->assertEquals(true, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInArray()
    {
        $parameter = new Parameter('array', 'value');
        $this->assertEquals(true, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInNull()
    {
        $parameter = new Parameter('null', 'value');
        $this->assertEquals(true, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInCallable()
    {
        $parameter = new Parameter('callable', 'value');
        $this->assertEquals(false, $parameter->isBuiltIn());
    }
    
    public function testIsBuiltInObject()
    {
        $parameter = new Parameter(Config::class, 'value');
        $this->assertEquals(false, $parameter->isBuiltIn());
    }
    
    public function testGetType()
    {
        $parameter = new Parameter('string', 'value');
        $this->assertEquals('string', $parameter->getType());
    }
    
    public function testGetName()
    {
        $parameter = new Parameter('string', 'value');
        $this->assertEquals('value', $parameter->getName());
    }
}
