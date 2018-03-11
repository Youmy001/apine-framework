<?php
/**
 * UriTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriTest extends TestCase
{
    /*
     * Factories
     */
    
    private function uriFactory() : Uri
    {
        return new Uri("https://example.com/test/as/15");
    }
    
    private function uriUsernameFactory() : Uri
    {
        return new Uri("smtp://username@example.com/test/as/15");
    }
    
    private function uriUsernamePasswordFactory() : Uri
    {
        return new Uri("smtp://username:password@example.com/test/as/15");
    }
    
    private function uriNotStandardPortFactory() : Uri
    {
        return new Uri("https://example.com:5670/test/as/15");
    }
    
    private function uriQueryStringFragmentFactory() : Uri
    {
        return new Uri("https://example.com/test?as=15#fragment");
    }
    
    /**
     * Tests
     */
    
    public function testGetScheme()
    {
        $this->assertEquals('https', $this->uriFactory()->getScheme());
    }
    
    public function testWithScheme()
    {
        $uri = $this->uriFactory()->withScheme('http');
        $this->assertAttributeEquals('http', 'scheme', $uri);
    
        $uri = $this->uriFactory()->withScheme('http://');
        $this->assertAttributeEquals('http', 'scheme', $uri);
    
        $uri = $this->uriFactory()->withScheme('');
        $this->assertAttributeEquals('', 'scheme', $uri);
    }
    
    public function testWithSchemeInvalidScheme()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = $this->uriFactory()->withScheme('invalid');
    }
    
    public function testWithSchemeInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = $this->uriFactory()->withScheme(450);
    }
    
    public function testGetAuthority()
    {
        $this->assertEquals('example.com', $this->uriFactory()->getAuthority());
    }
    
    public function testGetAuthorityUsername()
    {
        $this->assertEquals(
            "username@example.com",
            $this->uriUsernameFactory()->getAuthority()
        );
    }
    
    public function testGetAuthorityUsernamePassword()
    {
        $this->assertEquals(
            "username:password@example.com",
            $this->uriUsernamePasswordFactory()->getAuthority()
        );
    }
    
    public function testGetAuthorityNonStandardPort()
    {
        $this->assertEquals('example.com:5670', $this->uriNotStandardPortFactory()->getAuthority());
    }
    
    public function testGetUserInfo()
    {
        $this->assertEquals('', $this->uriFactory()->getUserInfo());
    }
    
    public function testGetUserInfoUsername()
    {
        $this->assertEquals('username', $this->uriUsernameFactory()->getUserInfo());
    }
    
    public function testGetUserInfoUsernamePassword()
    {
        $this->assertEquals('username:password', $this->uriUsernamePasswordFactory()->getUserInfo());
    }
    
    public function testWithUserInfo()
    {
        $uri = $this->uriFactory()->withUserInfo('youmy','pa55w0R4');
        $this->assertAttributeEquals('youmy', 'username', $uri);
        $this->assertAttributeEquals('pa55w0R4', 'password', $uri);
    }
    
    public function testGetHost()
    {
        $this->assertEquals('example.com', $this->uriFactory()->getHost());
    }
    
    public function testWithHost()
    {
        $uri = $this->uriFactory()->withHost('vocalvideo.net');
        $this->assertAttributeEquals('vocalvideo.net', 'host', $uri);
    }
    
    public function testWithHostInvalidHost()
    {
        $invalid_host = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.bb';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Hostname ' . $invalid_host);
        $uri = $this->uriFactory()->withHost($invalid_host);
    }
    
    public function testGetPort()
    {
        $this->assertEquals('5670', $this->uriNotStandardPortFactory()->getPort());
    }
    
    public function testWithPort()
    {
        $uri = $this->uriFactory()->withPort(459);
        $this->assertAttributeEquals(459, 'port', $uri);
    }
    
    public function testWithPortInvalidPort()
    {
        $this->expectException(TypeError::class);
        $uri = $this->uriFactory()->withPort("Totally a port number");
    }
    
    public function testWithPortUnderOne()
    {
        $bad_port = -1;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Port Number ' . $bad_port);
        $uri = $this->uriFactory()->withPort($bad_port);
    }
    
    public function testWithPortOverLimit()
    {
        $bad_port = 65555;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Port Number ' . $bad_port);
        $uri = $this->uriFactory()->withPort($bad_port);
    }
    
    public function testGetPath()
    {
        $this->assertEquals('/test/as/15', $this->uriFactory()->getPath());
    }
    
    public function testWithPath()
    {
        $uri = $this->uriFactory()->withPath('/example/test/as/15');
        $this->assertAttributeEquals('/example/test/as/15', 'path', $uri);
    }
    
    public function testWithPathInvalidPath()
    {
        $invalid_path = 'example/test/as/15';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Path ' . $invalid_path);
        $uri = $this->uriFactory()->withPath($invalid_path);
    }
    
    public function testGetQuery()
    {
        $this->assertEquals('as=15', $this->uriQueryStringFragmentFactory()->getQuery());
    }
    
    public function testWithQuery()
    {
        $uri = $this->uriFactory()->withQuery('as=15&other=something');
        $this->assertAttributeEquals('as=15&other=something', 'query', $uri);
    }
    
    public function testGetFragment()
    {
        $this->assertEquals('fragment', $this->uriQueryStringFragmentFactory()->getFragment());
    }
    
    public function testWithFragment()
    {
        $uri = $this->uriFactory()->withFragment('something');
        $this->assertAttributeEquals('something', 'fragment', $uri);
    }
    
    public function test__toString()
    {
        $string = 'https://user:password@example.com:5670/test/as/15?page=30&other=something#secondary';
        $this->assertEquals($string, (string)(new Uri($string)));
    
        $uri = $this->uriFactory()->withPath('/example/test/as/15');
        $this->assertEquals('https://example.com/example/test/as/15', $uri);
        
        $uri = $uri->withPort(4327);
        $this->assertEquals('https://example.com:4327/example/test/as/15', $uri);
    }
}
