<?php
/**
 * InjectableDataTraitTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Views\InjectableDataTrait;
use PHPUnit\Framework\TestCase;

class InjectableDataTraitTest extends TestCase
{
    /**
     * @var InjectableDataTrait
     */
    private $object;
    
    protected function setUp()
    {
        $this->object = $this->getMockForTrait(InjectableDataTrait::class);
    }
    
    public function testAddAttribute()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $mock->addAttribute('name', 'value');
        $this->assertArrayHasKey('name', $this->getObjectAttribute($mock, 'attributes'));
    }
    
    public function testGetAttribute()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $mock->addAttribute('name', 'value');
        $this->assertNotNull($mock->getAttribute('name'));
    }
    
    public function testGetAttributeNotExists()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $this->assertNull($mock->getAttribute('name'));
    }
    
    public function testGetAttributeDefaultValue()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $mock->addAttribute('name', 'value');
        $this->assertEquals('value', $mock->getAttribute('name', 'default'));
    }
    
    public function testGetAttributeNotExistsDefaultValue()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $this->assertEquals('default', $mock->getAttribute('name', 'default'));
    }
    
    public function testGetAttributes()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $mock->addAttribute('name', 'value');
        $this->assertInternalType('array', $mock->getAttributes());
        $this->assertEquals(['name' => 'value'], $mock->getAttributes());
    }
    
    public function testGetAttributesEmpty()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $this->assertInternalType('array', $mock->getAttributes());
    }
    
    public function testRemoveAttribute()
    {
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $mock->addAttribute('name', 'value');
        $this->assertArrayHasKey('name', $this->getObjectAttribute($mock, 'attributes'));
        $mock->removeAttribute('name');
        $this->assertArrayNotHasKey('name', $this->getObjectAttribute($mock, 'attributes'));
    }
    
    public function testSetAttributes()
    {
        $array = [
            'name' => 'value',
            'boolean' => false,
            'integers' => [1,2,3]
        ];
        
        $mock = $this->getMockForTrait(InjectableDataTrait::class);
        $mock->setAttributes($array);
        $this->assertAttributeNotEmpty('attributes', $mock);
        $this->assertAttributeEquals($array, 'attributes', $mock);
    }
}
