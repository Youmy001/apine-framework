<?php
/**
 * FileViewTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Views\FileView;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class FileViewTest extends TestCase
{
    private static $filename = 'response.txt';
    
    /**
     * @beforeClass
     */
    public static function createFile()
    {
        $resource = fopen(self::$filename, 'w+');
        fwrite($resource, 'This is a text file.');
        fclose($resource);
    }
    
    /**
     * @afterClass
     */
    public static function deleteFile()
    {
        unlink(self::$filename);
    }
    
    public function testConstructor()
    {
        $view = new FileView(self::$filename);
        
        $this->assertAttributeInternalType('resource', 'resource', $view);
        $this->assertAttributeEquals(self::$filename, 'path', $view);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /File (.*?) not found/
     */
    public function testConstructorFileNotExist()
    {
        $view = new FileView('notfound.jpg');
    }
    
    public function testSetFile()
    {
        $view = new FileView(self::$filename);
        $view->setFile(self::$filename);
    
        $this->assertAttributeInternalType('resource', 'resource', $view);
        $this->assertAttributeEquals(self::$filename, 'path', $view);
    
        $this->assertEquals('text/plain', $this->getObjectAttribute($view, 'headers')['content-type']['value']);
        $this->assertEquals(20, $this->getObjectAttribute($view, 'headers')['content-length']['value']);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /File (.*?) not found/
     */
    public function testSetFileFileNotExists()
    {
        $view = new FileView(self::$filename);
        $view->setFile('notfound.jpg');
    }
    
    public function testRespond()
    {
        $view = new FileView(self::$filename);
        $response = $view->respond();
    
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('text/plain', $response->getHeaderLine('content-type'));
        $this->assertEquals(20, $response->getHeaderLine('content-length'));
    
        $body = $response->getBody();
        $body->rewind();
        $content = $body->getContents();
        
        $this->assertEquals('This is a text file.', $content);
    }
}
