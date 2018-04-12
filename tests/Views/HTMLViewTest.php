<?php
/**
 * HTMLViewTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Error\ErrorHandler;
use Apine\Core\Views\HTMLView;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HTMLViewTest extends TestCase
{
    private static $filename = 'response';
    
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
    public static function createFile()
    {
        $resource = fopen(self::$filename . '.html', 'w+');
        fwrite($resource, '<h1>Header in HTML</h1>');
        fclose($resource);
    
        mkdir('config');
    }
    
    /**
     * @afterClass
     */
    public static function deleteFile()
    {
        if (file_exists(self::$filename . '.html')) {
            unlink(self::$filename . '.html');
        }
        
        if (file_exists('config/twig.json')) {
            unlink('config/twig.json');
        }
    
        rmdir('config');
    }
    
    public function testConstructor()
    {
        $view = new HTMLView(self::$filename);
        
        $this->assertAttributeInternalType('string', 'file', $view);
        $this->assertAttributeEquals(self::$filename . '.html', 'file', $view);
    }
    
    public function testConstructorCustomStatusCode()
    {
        $view = new HTMLView(self::$filename, [], 340);
        
        $this->assertAttributeInternalType('integer', 'statusCode', $view);
        $this->assertAttributeEquals(340, 'statusCode', $view);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorCustomStatusCodeInvalid()
    {
        $view = new HTMLView(self::$filename, [], 640);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found
     */
    public function testConstructorTemplateFileNotFound()
    {
        $view = new HTMLView('notfound');
    }
    
    public function testConstructorData()
    {
        $view = new HTMLView(self::$filename, ['name' => 'value']);
        $this->assertAttributeNotEmpty('attributes', $view);
    }
    
    public function testSetFile()
    {
        $view = new HTMLView(self::$filename);
    
        $resource = fopen(self::$filename . '.twig', 'w+');
        fwrite($resource, '<h1>Header in HTML through Twig</h1>');
        fclose($resource);
        
        $view->setFile(self::$filename);
        
        $this->assertAttributeEquals(self::$filename . '.twig', 'file', $view);
    
        unlink(self::$filename . '.twig');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found
     */
    public function testSetFileFileNotFound()
    {
        $view = new HTMLView(self::$filename, ['name' => 'value']);
        $view->setFile('notfound');
    }
    
    public function testRespond()
    {
        $view = new HTMLView(self::$filename, ['name' => 'value']);
        $view->addHeader('fake', 'value');
        $response = $view->respond();
    
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertTrue($response->hasHeader('fake'));
    }
}
