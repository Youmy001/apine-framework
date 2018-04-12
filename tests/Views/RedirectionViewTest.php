<?php
/**
 * RedirectionViewTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Http\Factories\UriFactory;
use Apine\Core\Views\RedirectionView;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class RedirectionViewTest extends TestCase
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
    
    public function testConstructors()
    {
        $uri = (new UriFactory())->createUri('https://google.ca');
        $redirection = new RedirectionView($uri);
        
        $this->assertAttributeInstanceOf(UriInterface::class, 'uri', $redirection);
        $this->assertEquals('https://google.ca', (string) $this->getObjectAttribute($redirection, 'uri'));
    }
    
    public function testCreateFromPathWithCustomServer()
    {
        $redirection = RedirectionView::createFromPath('/home', 301, self::$server);
        $this->assertEquals('https://example.com/home?home=cat', (string) $this->getObjectAttribute($redirection, 'uri'));
    }
    
    public function testCreateFromPathWithGlobalServer()
    {
        global $_SERVER;
    
        foreach (self::$server as $key => $value) {
            $_SERVER[$key] = $value;
        }
        
        $redirection = RedirectionView::createFromPath('/home', 301);
        $this->assertEquals('https://example.com/home?home=cat', (string) $this->getObjectAttribute($redirection, 'uri'));
    }
    
    public function testRespond()
    {
        $redirection = RedirectionView::createFromPath('/home', 301, self::$server);
        $redirection->addHeader('Content-Type', 'text/html');
        $response = $redirection->respond();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
