<?php
/**
 * MessageTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Message;
use Apine\Core\Http\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class MessageTest extends TestCase
{
    /**
     * @var Message
     */
    private $object;
    
    public function setUp()
    {
        $this->object = $this->getMockForAbstractClass(Message::class);
    }
    
    /**
     * @covers Message::withProtocolVersion()
     * @return Message
     */
    public function testWithProtocolVersion()
    {
        $message = $this->object->withProtocolVersion('1.0');
        $this->assertAttributeEquals('1.0', 'protocol', $message);
        
        return $message;
    }
    
    /**
     * @covers Message::getProtocolVersion()
     * @depends testWithProtocolVersion
     *
     * @param Message $message
     */
    public function testGetProtocolVersion(Message $message)
    {
        $this->assertEquals('1.0', $message->getProtocolVersion());
    }
    
    /**
     * @covers Message::withHeader()
     * @return Message
     */
    public function testWithHeader()
    {
        $message = $this->object->withHeader('Content-Type', 'application/json');
        $this->assertAttributeNotEmpty('headers', $message);
        
        return $message;
    }
    
    /**
     * @covers Message::getHeaders()
     * @depends testWithHeader
     *
     * @param Message $message
     */
    public function testGetHeaders(Message $message)
    {
        $this->assertInternalType('array', $message->getHeaders());
    }
    
    /**
     * @covers Message::hasHeader()
     * @depends testWithHeader
     *
     * @param Message $message
     */
    public function testHasHeader(Message $message)
    {
        $this->assertEquals(true, $message->hasHeader('Content-Type'));
    }
    
    /**
     * @covers Message::getHeader()
     * @depends testWithHeader
     *
     * @param Message $message
     */
    public function testGetHeader(Message $message)
    {
        $this->assertEquals('application/json', $message->getHeader('Content-Type'));
    }
    
    /**
     * @covers Message::getHeaderLine()
     * @depends testWithHeader
     *
     * @param Message $message
     */
    public function testGetHeaderLine(Message $message)
    {
        $this->assertEquals('application/json', $message->getHeaderLine('Content-Type'));
    }
    
    /**
     * @covers Message::withAddedHeader()
     * @depends testWithHeader
     *
     * @param Message $message
     */
    public function testWithAddedHeader(Message $message)
    {
        $new = $message->withAddedHeader('Accept-Language', ['en-US', 'fr-CA']);
        $this->assertEquals(true, $new->hasHeader('Accept-Language'));
        $this->assertEquals(true, $new->hasHeader('Content-Type'));
        
        return $new;
    }
    
    /**
     * @covers Message::withoutHeader()
     * @depends testWithAddedHeader
     *
     * @param Message $message
     */
    public function testWithoutHeader(Message $message)
    {
        $this->assertEquals(true, $message->hasHeader('Accept-Language'));
        
        $new = $message->withoutHeader('Accept-Language');
        $this->assertEquals(false, $new->hasHeader('Accept-Language'));
    }
    
    /**
     * @covers Message::withBody()
     *
     * @return Message
     */
    public function testWithBody()
    {
        $resource = fopen('php://memory','r+');
        fwrite($resource, 'Test Body');
        $stream = new Stream($resource);
        
        $message = $this->object->withBody($stream);
        $this->assertAttributeInstanceOf(StreamInterface::class, 'body', $message);
        return $message;
    }
    
    /**
     * @covers Message::getBody()
     * @depends testWithBody
     *
     * @param Message $message
     */
    public function testGetBody(Message $message)
    {
        $this->assertInstanceOf(StreamInterface::class, $message->getBody());
        $this->assertEquals('Test Body', (string)$message->getBody());
    }
}
