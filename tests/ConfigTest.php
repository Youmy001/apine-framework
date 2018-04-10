<?php
/**
 * ConfigTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Config;
use Apine\Core\Error\ErrorHandler;
use Apine\Core\Json\Json;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    static private $filename = './testConfig';
    
    static private $jsonString = '{
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
    
    public function setUp()
    {
        ErrorHandler::set(); // Set up error handling so that notice in file manipulation convert to Exception
    }
    
    public function tearDown()
    {
        ErrorHandler::unset();
    }
    
    /**
     * @beforeClass
     */
    public static function createTestFile()
    {
        $resource = fopen(self::$filename, 'w');
        fwrite($resource, self::$jsonString);
        fclose($resource);
    }
    
    /**
     * @afterClass
     */
    public static function deleteTestFile()
    {
        unlink(self::$filename);
    }
    
    public function testConstructor()
    {
        $config = new Config(self::$filename);
        
        $this->assertAttributeEquals(self::$filename, 'path', $config);
        $this->assertAttributeInstanceOf(Json::class, 'settings', $config);
    }
    
    public function testGetPath()
    {
        $config = new Config(self::$filename);
        
        $this->assertEquals(self::$filename, $config->getPath());
    }
    
    public function testIsset()
    {
        $config = new Config(self::$filename);
        
        $this->assertEquals(true, $config->__isset('name'));
        $this->assertEquals(false, $config->__isset('fake'));
    
        $this->assertEquals(true, isset($config->name));
        $this->assertEquals(false, isset($config->fake));
    }
    
    public function testGet()
    {
        $config = new Config(self::$filename);
        
        $this->assertEquals('value', $config->__get('name'));
        $this->assertEquals('value', $config->name);
    }
    
    /**
     * @expectedException \ErrorException
     */
    public function testGetValueNotSet()
    {
        $config = new Config(self::$filename);
        $this->assertEquals('value', $config->fake);
    }
    
    public function testSet()
    {
        $config = new Config(self::$filename);
        
        $this->assertFalse(isset($config->value));
        
        $config->value = 'test';
    
        $this->assertTrue(isset($config->value));
        $this->assertEquals('test', $config->value);
    }
    
    public function testSave()
    {
        $path = '.newConfig';
    
        $this->assertFileNotExists($path);
        
        $config = new Config($path);
        $config->name = 'value';
        $config->save();
        
        $this->assertFileExists($path);
        
        $json = json_decode(file_get_contents($path));
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        
        unlink($path);
    }
}
