<?php
/**
 * JsonStoreTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Json\Json;
use Apine\Core\Json\JsonStore;
use Apine\Core\Json\JsonStoreFileNotFoundException;
use PHPUnit\Framework\TestCase;

class JsonStoreTest extends TestCase
{
    static private $jsonFileName = './jsonFile';
    
    static private $invalidFileName = './invalidFile';
    
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
    
    /**
     * @var JsonStore
     */
    private $store;
    
    public function setUp()
    {
        $this->store = new JsonStore();
    }
    
    /**
     * @beforeClass
     */
    public static function createTestFile()
    {
        $resource = fopen(self::$jsonFileName, 'w');
        fwrite($resource, self::$jsonString);
        fclose($resource);
    
        $invalidResource = fopen(self::$invalidFileName, 'w');
        fwrite($invalidResource, 'Not Json');
        fclose($invalidResource);
    }
    
    /**
     * @afterClass
     */
    public static function deleteTestFile()
    {
        unlink(self::$jsonFileName);
        unlink(self::$invalidFileName);
        unlink('./generatedFile');
    }
    
    public function testGetInstance()
    {
        $this->assertInstanceOf(JsonStore::class, $this->store->getInstance());
    }
    
    public function testGet()
    {
        $json = $this->store->get(self::$jsonFileName);
        $this->assertInstanceOf(Json::class, $json);
    }
    
    /**
     * @expectedException Apine\Core\Json\JsonStoreFileNotFoundException
     */
    public function testGetFileNotFound()
    {
        $json = $this->store->get('notfile.json');
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testGetNotValidJsonFormat()
    {
        $json = $this->store->get(self::$invalidFileName);
    }
    
    public function testSet()
    {
        $this->store->set(self::$invalidFileName, self::$jsonString);
        
        $this->assertInstanceOf(Json::class, $this->store->get(self::$invalidFileName));
    }
    
    public function testSetOnNewFile()
    {
        $this->store->set('generatedFile', self::$jsonString);
    
        $this->assertInstanceOf(Json::class, $this->store->get('generatedFile'));
    }
    
    public function testWrite()
    {
        $this->store->__destruct();
        //$this->deleteTestFile();
        $this->assertFileExists(self::$jsonFileName);
        $this->assertFileExists(self::$invalidFileName);
        $this->assertFileExists('./generatedFile');
    }
}
