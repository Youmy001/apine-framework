<?php
/**
 * UriFactoryTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Http\Factories\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends TestCase
{
    /**
     * @var UriFactory
     */
    private $factory;
    
    private $server = [
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
    
    public function setUp()
    {
        $this->factory = new UriFactory();
    }
    
    public function testCreateUri()
    {
        $uri = $this->factory->createUri('https://google.com');
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
    
    public function testCreateUriFromArray()
    {
        $uri = $this->factory->createUriFromArray($this->server);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
    
    public function testCreateUriFromArrayEmptyPath()
    {
        $server = $this->server;
        $server['REQUEST_URI'] = '/?home=cat';
        
        $uri = $this->factory->createUriFromArray($server);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
    
    public function testCreateUriFromArrayHasQueryStringHeaderDespiteNotHavingQueryInRequestURI()
    {
        $server = $this->server;
        $server['REQUEST_URI'] = '/test/example';
        $server['QUERY_STRING'] = 'apine-request=/test/example';
    
        $uri = $this->factory->createUriFromArray($server);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
    
    public function testCreateUriFromArrayServerNameNotHost()
    {
        $server = $this->server;
        unset($server['HTTP_HOST']);
        
        $uri = $this->factory->createUriFromArray($server);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
    
    public function testCreateUriFromArrayServerAddressNotHost()
    {
        $server = $this->server;
        unset($server['HTTP_HOST'], $server['SERVER_NAME']);
        
        $uri = $this->factory->createUriFromArray($server);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot determine URI from array
     */
    public function testCreateUriFromArrayNoHost()
    {
        $server = $this->server;
        unset($server['HTTP_HOST'], $server['SERVER_NAME'], $server['SERVER_ADDR']);
        $uri = $this->factory->createUriFromArray($server);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }
}
