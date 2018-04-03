<?php
/**
 * StreamFactoryTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Factories\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamFactoryTest extends TestCase
{
    /**
     * @var StreamFactory
     */
    private $factory;
    
    public function setUp()
    {
        $this->factory = new StreamFactory();
    
        $fakeresource = fopen('streamFile','w');
        fwrite($fakeresource, 'Test of PSR-7 Streams');
        fclose($fakeresource);
    }
    
    public function tearDown()
    {
        unlink('streamFile');
    }
    
    public function testCreateStream()
    {
        $stream = $this->factory->createStream('Some content');
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
    
    public function testCreateStreamFromResource()
    {
        $resource = fopen('php://memory', 'r+');
        $stream = $this->factory->createStreamFromResource($resource);
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
    
    public function testCreateStreamFromFile()
    {
        $stream = $this->factory->createStreamFromFile('streamFile', 'r');
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found
     */
    public function testCreateStreamFromFileNotFound()
    {
        $stream = $this->factory->createStreamFromFile('fakeFile', 'r');
    }
}
