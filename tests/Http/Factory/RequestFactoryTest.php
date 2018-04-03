<?php
/**
 * RequestFactoryTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Http\Factories\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestFactoryTest extends TestCase
{
    /**
     * @var RequestFactory
     */
    private $factory;
    
    private $server = [
        'REQUEST_METHOD' => 'GET',
        'SERVER_PROTOCOL' => 'HTTP/1.1',
        'HTTP_HOST' => 'google.com',
    ];
    
    public function setUp()
    {
        $this->factory = new RequestFactory();
    }
    
    public function testCreateServerRequest()
    {
        $request = $this->factory->createServerRequest('GET', 'https://google.com');
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
    }
    
    public function testCreateServerRequestFromGlobals()
    {
        $request = $this->factory->createServerRequestFromArray($this->server);
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot determine valid request method from array
     */
    public function testCreateServerRequestFromGlobalsCannotDetermineValidMethod()
    {
        $request = $this->factory->createServerRequestFromArray([]);
    }
    
    public function testCreateServerRequestFromArray()
    {
        $request = $this->factory->createServerRequestFromGlobals(
            $this->server
        );
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
    }
}
