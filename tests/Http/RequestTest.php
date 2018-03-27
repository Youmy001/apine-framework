<?php
/**
 * RequestTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Request;
use Apine\Core\Http\UploadedFile;
use Apine\Core\Http\Uri;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    
    private function requestFactory() : Request
    {
        $uri = new Uri('http://example.com/test/23?test=123');
        return new Request(
            'GET',
            $uri,
            [],
            null,
            '1,1',
            $_SERVER
        );
    }
    
    public function testAddsHostHeader()
    {
        $request = $this->requestFactory();
        $this->assertEquals('example.com', $request->getHeaderLine('Host'));
    }
    
    /**
     * @covers Request::getRequestTarget()
     */
    public function testGetRequestTarget()
    {
        $this->assertEquals('/test/23?test=123', $this->requestFactory()->getRequestTarget());
    }
    
    /**
     * @covers Request::withRequestTarget()
     */
    public function testWithRequestTarget()
    {
        $request = $this->requestFactory()->withRequestTarget('/giza?page=123');
        $this->assertEquals('/giza?page=123', $request->getRequestTarget());
    }
    
    /**
     * @covers Request::withRequestTarget()
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid target provided. Request targets may not contain whitespaces.
     */
    public function testWithRequestTargetInvalid()
    {
        $this->requestFactory()->withRequestTarget('/trest asdfas');
    }
    
    /**
     * @covers Request::getMethod()
     */
    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->requestFactory()->getMethod());
    }
    
    /**
     * @covers Request::withMethod()
     */
    public function testWithMethod()
    {
        $request = $this->requestFactory()->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }
    
    /**
     * @covers Request::getUri()
     */
    public function testGetUri()
    {
        $uri = $this->requestFactory()->getUri();
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertEquals('example.com', $uri->getHost());
    }
    
    /**
     * @covers Request::withUri()
     */
    public function testWithUri()
    {
        $uri = new Uri('https://google.ca');
        $request = $this->requestFactory()->withUri($uri);
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals($uri, $request->getUri());
    }
    
    /**
     * @covers Request::getServerParams()
     */
    public function testGetServerParams()
    {
        $this->assertEquals($_SERVER, $this->requestFactory()->getServerParams());
    }
    
    /**
     * @covers Request::getCookieParams()
     */
    public function testGetCookieParams()
    {
        $this->assertEquals([], $this->requestFactory()->getCookieParams());
    }
    
    /**
     * @covers Request::withCookieParams()
     */
    public function testWithCookieParams()
    {
        $array = ['cookie' => 'value'];
        $request = $this->requestFactory()->withCookieParams($array);
        
        $this->assertEquals($array, $request->getCookieParams());
    }
    
    public function testGetQueryParams()
    {
        $this->assertEquals(['test' => 123], $this->requestFactory()->getQueryParams());
    }
    
    public function testWithQueryParams()
    {
        $array = ['query' => 'test', 'test' => 5678];
        $request = $this->requestFactory()->withQueryParams($array);
        $this->assertEquals($array, $request->getQueryParams());
    }
    
    public function testGetUploadedFiles()
    {
        $this->assertEquals([], $this->requestFactory()->getUploadedFiles());
    }
    
    public function testWithUploadedFiles()
    {
        $resource = fopen('php://memory', 'r+');
        fwrite($resource, 'test');
        $file = new UploadedFile($resource, 4, 0, 'text.txt', 'text/plain');
        
        $request = $this->requestFactory()->withUploadedFiles([$file]);
        $this->assertEquals([$file], $request->getUploadedFiles());
    }
    
    public function testGetParsedBody()
    {
        $this->assertEquals(null, $this->requestFactory()->getParsedBody());
    }
    
    public function testGetParsedBodyJson()
    {
        $json_array = [
            'title' => 'value',
            'array' => [
                1,
                2
            ]
        ];
        $json_string = json_encode($json_array);
        
        $request = new Request(
            'POST',
            'http://example.com/test',
            ['Content-Type' => 'application/json'],
            $json_string
        );
        
        $this->assertEquals($json_array, $request->getParsedBody());
    }
    
    public function testWithParsedBody()
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $request = $this->requestFactory()->withParsedBody($array);
        $this->assertEquals($array, $request->getParsedBody());
    }
    
    public function testWithAttribute()
    {
        $request = $this->requestFactory()->withAttribute('name', 'value');
        $this->assertAttributeEquals(['name' => 'value'], 'attributes', $request);
        
        return $request;
    }
    
    /**
     * @depends testWithAttribute
     */
    public function testGetAttributes(Request $request)
    {
        $this->assertInternalType('array', $request->getAttributes());
        $this->assertEquals(['name' => 'value'], $request->getAttributes());
    }
    
    /**
     * @depends testWithAttribute
     */
    public function testGetAttribute(Request $request)
    {
        $this->assertEquals('value', $request->getAttribute('name'));
    }
    
    /**
     * @depends testWithAttribute
     */
    public function testGetAttributeNonExisting(Request $request)
    {
        $this->assertEquals(null, $request->getAttribute('title'));
    }
    
    /**
     * @depends testWithAttribute
     */
    public function testGetAttributeNonExistingWithDefault(Request $request)
    {
        $this->assertEquals('default', $request->getAttribute('none', 'default'));
    }
    
    /**
     * @depends testWithAttribute
     */
    public function testWithoutAttribute(Request $request)
    {
        $request = $request->withoutAttribute('name');
        $this->assertInternalType('array', $request->getAttributes());
        $this->assertArrayNotHasKey('name', $request->getAttributes());
    }
    
    public function testIsHttps()
    {
        $this->assertFalse($this->requestFactory()->isHttps());
    
        $request = new Request(
            'GET',
            'https://example.com'
        );
        
        $this->assertTrue($request->isHttps());
    }
    
    public function testIsAPICall()
    {
        $this->assertFalse($this->requestFactory()->isAPICall());
    }
    
    public function testIsAjax()
    {
        $this->assertFalse($this->requestFactory()->isAjax());
        
        $request = $this->requestFactory()->withHeader('X-Requested-With', 'XMLHttpRequest');
        $this->assertTrue($request->isAjax());
    }
    
    public function testIsGet()
    {
        $this->assertTrue($this->requestFactory()->isGet());
    }
    
    public function testIsPost()
    {
        $request = $this->requestFactory()->withMethod('POST');
        $this->assertTrue($request->isPost());
    }
    
    public function testIsPut()
    {
        $request = $this->requestFactory()->withMethod('PUT');
        $this->assertTrue($request->isPut());
    }
    
    public function testIsDelete()
    {
        $request = $this->requestFactory()->withMethod('DELETE');
        $this->assertTrue($request->isDelete());
    }
}
