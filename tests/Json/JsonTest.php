<?php
/**
 * JsonTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Json\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    private $jsonString = '{
        "name" : "value",
        "array" : [
            1,
            2,
            3
        ],
        "object" : {
            "name" : "object",
            "array" : [
                {
                    "number" : "one",
                    "value" : 1
                },
                {
                    "number" : "two",
                    "value" : 2
                },
                {
                    "number" : "three",
                    "value" : 3
                }
            ]
        }
    }';
    
    private $jsonArray = [
        "name" => "value",
        "array" => [1,2,3],
        "object" => [
            "name" => "object",
            "array" => [
                [
                    "number" => "one",
                    "value" => 1
                ],
                [
                    "number" => "two",
                    "value" => 2
                ],
                [
                    "number" => "three",
                    "value" => 3
                ]
            ]
        ]
    ];
    
    public function testConstructFromString()
    {
        $json = new Json($this->jsonString);
        
        $this->assertAttributeInstanceOf(StdClass::class, "data", $json);
    }
    
    public function testConstructFromArray()
    {
        $json = new Json($this->jsonArray);
    
        $this->assertAttributeInstanceOf(StdClass::class, "data", $json);
    }
    
    public function testConstructEmpty()
    {
        $json = new Json("");
    
        $this->assertAttributeInstanceOf(StdClass::class, "data", $json);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid JSON string
     */
    public function testConstructFromInvalidString()
    {
        $json = new Json("I am not a JSON string");
    }
    
    public function testGet()
    {
        $json = new Json($this->jsonString);
        
        $this->assertNotNull($json->name);
        $this->assertEquals("value", $json->name);
    }
    
    public function testGetDeeperLevel()
    {
        $json = new Json($this->jsonString);
    
        $this->assertNotNull($json->object->name);
        $this->assertEquals("object", $json->object->name);
    }
    
    public function testSet()
    {
        $json = new Json($this->jsonString);
        
        $json->name = "json";
        
        $this->assertEquals("json", $json->name);
    }
    
    public function testSetDeeperLevel()
    {
        $json = new Json($this->jsonString);
        $array = [1,2,3];
        
        $json->object->numbers = $array;
        
        $this->assertNotNull($json->object->numbers);
        $this->assertEquals($array, $json->object->numbers);
    }
    
    public function testIsset()
    {
        $json = new Json($this->jsonString);
        
        $this->assertTrue(isset($json->object->name));
    }
    
    public function testUnset()
    {
        $json = new Json($this->jsonString);
        
        $this->assertTrue(isset($json->object));
        unset($json->object);
        $this->assertFalse(isset($json->object));
    }
    
    public function testToString()
    {
        $json = new Json($this->jsonArray);
        $this->assertInternalType('string', (string) $json);
    }
}
