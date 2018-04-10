<?php
/**
 * URLHelperTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Utility\URLHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use const \Apine\Core\PROTOCOL_HTTP;
use const \Apine\Core\PROTOCOL_HTTPS;

class URLHelperTest extends TestCase
{
    private static $server = [
        'HTTPS' => 'on',
        'REQUEST_METHOD' => 'GET',
        'SERVER_PROTOCOL' => 'HTTP/1.1',
        'HTTP_HOST' => 'example.com',
        'REQUEST_URI' => '/test/example?home=cat',
        'QUERY_STRING' => 'home=cat',
        'SERVER_NAME' => 'example.com',
        'SERVER_ADDR' => '127.0.0.1',
        'SERVER_PORT' => 443
    ];
    
    public function testConstructor()
    {
        $helper = new URLHelper(self::$server);
        
        $this->assertAttributeInstanceOf(UriInterface::class, 'uri', $helper);
        $this->assertAttributeEquals("/test/example", 'path', $helper);
        $this->assertAttributeEquals("example.com", "mainAuthority", $helper);
        $this->assertAttributeEquals("example.com", "authority", $helper);
    }
    
    public function testConstructorHostHasSubDomain()
    {
        $server = self::$server;
        $server['HTTP_HOST'] = 'test.example.com';
        
        $helper = new URLHelper($server);
    
        $this->assertAttributeEquals("example.com", "mainAuthority", $helper);
        $this->assertAttributeEquals("test.example.com", "authority", $helper);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot determine URI from array
     */
    public function testConstructorArrayNotServerCompatible()
    {
        $helper = new URLHelper([]);
    }
    
    public function testConstructorGlobalServerArray()
    {
        global $_SERVER;
        
        foreach (self::$server as $key => $value) {
            $_SERVER[$key] = $value;
        }
        
        $helper = new URLHelper();
        $this->assertAttributeEquals("/test/example", 'path', $helper);
    }
    
    public function testPath()
    {
        $helper = new URLHelper(self::$server);
        $this->assertEquals('https://example.com/test/path', $helper->path('test/path'));
    }
    
    public function testPathForceHTTP()
    {
        $helper = new URLHelper(self::$server);
        $this->assertEquals('http://example.com/test/path', $helper->path('test/path', PROTOCOL_HTTP));
    }
    
    public function testPathForceHTTPS()
    {
        $server = self::$server;
        $server['HTTPS'] = 'off';
        
        $helper = new URLHelper($server);
        $this->assertEquals('https://example.com/test/path', $helper->path('test/path', PROTOCOL_HTTPS));
    }
    
    public function testResource()
    {
        $helper = new URLHelper(self::$server);
        $this->assertEquals('https://example.com/test/path.txt', $helper->resource('test/path.txt'));
    }
    
    public function testRelativePath()
    {
        $helper = new URLHelper(self::$server);
        $this->assertEquals('https://example.com/test/example/test/path', $helper->relativePath('test/path'));
    }
    
    public function testRelativePathForceHTTP()
    {
        $helper = new URLHelper(self::$server);
        $this->assertEquals('http://example.com/test/example/test/path', $helper->relativePath('test/path', PROTOCOL_HTTP));
    }
    
    public function testRelativePathForceHTTPS()
    {
        $server = self::$server;
        $server['HTTPS'] = 'off';
    
        $helper = new URLHelper($server);
        $this->assertEquals('https://example.com/test/example/test/path', $helper->relativePath('test/path', PROTOCOL_HTTPS));
    }
    
    public function testMainPath()
    {
        $server = self::$server;
        $server['HTTP_HOST'] = 'test.example.com';
        
        $helper = new URLHelper($server);
    
        $this->assertEquals('https://example.com/test/main', $helper->mainPath('test/main'));
    }
    
    public function testMainPathForceHTTP()
    {
        $server = self::$server;
        $server['HTTP_HOST'] = 'test.example.com';
        
        $helper = new URLHelper($server);
        
        $this->assertEquals('http://example.com/test/main', $helper->mainPath('test/main', PROTOCOL_HTTP));
    }
    
    public function testMainPathForceHTTPS()
    {
        $server = self::$server;
        $server['HTTPS'] = 'off';
        $server['HTTP_HOST'] = 'test.example.com';
        
        $helper = new URLHelper($server);
        
        $this->assertEquals('https://example.com/test/main', $helper->mainPath('test/main', PROTOCOL_HTTPS));
    }
    
    public function testGetCurrentPath()
    {
        $helper = new URLHelper(self::$server);
        
        $this->assertEquals('https://example.com/test/example', $helper->getCurrentPath());
    }
}
