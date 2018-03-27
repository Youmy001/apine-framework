<?php
/**
 * StreamTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    private function streamFactory()
    {
        $fakeresource = fopen('php://memory','rw');
        fwrite($fakeresource, 'Test of PSR-7 Streams');
        
        return new Stream($fakeresource);
    }
    
    private function streamReadOnlyFactory()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'UriTest.php';
        $resource = fopen($filename,'r');
        //fwrite($fakeresource, 'Test of PSR-7 Streams');
    
        return new Stream($resource);
    }
    
    private function streamWriteOnlyFactory()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'UriTest.php';
        $resource = fopen($filename,'a');
    
        return new Stream($resource);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Stream source is not a resource
     */
    public function testInvalidResource()
    {
        $stream = new Stream('fakefile');
    }
    
    public function testGetMetaData()
    {
        $stream = $this->streamReadOnlyFactory();
        $this->assertEquals('r', $stream->getMetadata('mode'));
        $this->assertEquals(null, $stream->getMetadata('creative'));
        $this->assertInternalType('array', $stream->getMetadata());
    }
    
    public function testGetMetaDataOnDetachedStream()
    {
        $stream = $this->streamReadOnlyFactory();
        $stream->detach();
        
        $this->assertNull($stream->getMetadata());
    }
    
    public function testIsWritable()
    {
        $this->assertEquals(true, $this->streamFactory()->isWritable());
    }
    
    public function testIsSeekable()
    {
        $this->assertEquals(true, $this->streamFactory()->isSeekable());
    }
    
    public function testIsReadable()
    {
        $this->assertEquals(true, $this->streamFactory()->isReadable());
    }
    
    public function testClose()
    {
        $stream = $this->streamFactory();
        $stream->close();
        
        $this->assertAttributeEquals(null, 'stream', $stream);
    }
    
    public function testDetach()
    {
        $stream = $this->streamFactory();
        $resource = $stream->detach();
        
        $this->assertAttributeEmpty('size', $stream);
        $this->assertEquals(false,$stream->isReadable());
        $this->assertEquals(false,$stream->isSeekable());
        $this->assertEquals(false,$stream->isWritable());
        $this->assertInternalType('resource', $resource);
    }
    
    public function testGetSize()
    {
        $this->assertEquals(strlen('Test of PSR-7 Streams'), $this->streamFactory()->getSize());
    }
    
    public function testGetSizeOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $stream->detach();
        
        $this->assertNull($stream->getSize());
    }
    
    public function testSeek()
    {
        $stream = $this->streamFactory();
        $stream->seek(10);
        $this->assertEquals(10, $stream->tell());
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Cannot seek to position (\d+) with whence (\d{1})/
     */
    public function testSeekOverStreamLength()
    {
        $stream = $this->streamFactory();
        $stream->seek(100);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is detached
     */
    public function testSeekOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $resource = $stream->detach();
        
        $stream->seek(10);
    }
    
    public function testTell()
    {
        // The pointer is at the end of the stream because I write some content in it
        $this->assertEquals(21, $this->streamFactory()->tell());
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is detached
     */
    public function testTellOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $resource = $stream->detach();
    
        $position = $stream->tell();
    }
    
    public function testRewind()
    {
        $stream = $this->streamFactory();
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }
    
    public function testRead()
    {
        $stream = $this->streamFactory();
        $stream->rewind();
        
        $this->assertEquals('Test', $stream->read(4));
    }
    
    public function testReadLengthZero()
    {
        $stream = $this->streamFactory();
        $stream->rewind();
        
        $this->assertEmpty($stream->read(0));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Length is not an integer
     */
    public function testReadInvalidLength()
    {
        $stream = $this->streamFactory();
        $stream->read('five');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Length cannot be negative
     */
    public function testReadNegativeLength()
    {
        $stream = $this->streamFactory();
        $stream->read(-4);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is not readable
     */
    public function testReadNotReadable()
    {
        $stream = $this->streamWriteOnlyFactory();
        $reads = $stream->read(4);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is detached
     */
    public function testReadOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $stream->detach();
        $stream->read(4);
    }
    
    public function testGetContents()
    {
        $stream = $this->streamFactory();
        $stream->rewind();
        $this->assertEquals('Test of PSR-7 Streams', $stream->getContents());
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot read the content of stream
     */
    public function testGetContentsNotReadable()
    {
        $stream = $this->streamWriteOnlyFactory();
        $contents = $stream->getContents();
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is detached
     */
    public function testGetContentsOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $resource = $stream->detach();
        
        $contents = $stream->getContents();
    }
    
    public function testEOF()
    {
        $stream = $this->streamReadOnlyFactory();
        $contents = $stream->getContents();
        $this->assertEquals(true, $stream->eof());
        
        $stream->rewind();
        $this->assertEquals(false, $stream->eof());
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is detached
     */
    public function testEOFOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $resource = $stream->detach();
        
        $eof = $stream->eof();
    }
    
    public function testWrite()
    {
        $stream = $this->streamFactory();
        
        $this->assertEquals(4, $stream->write('1234'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument must be of type string
     */
    public function testWriteInvalidInput()
    {
        $stream = $this->streamFactory();
        $stream->write([0=>null]);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is not writable
     */
    public function testWriteNotWritable()
    {
        $stream = $this->streamReadOnlyFactory();
        $stream->write('1234');
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stream is detached
     */
    public function testWriteOnDetachedStream()
    {
        $stream = $this->streamFactory();
        $stream->detach();
        $stream->write('1234');
    }
    
    public function test__toString()
    {
        $this->assertEquals('Test of PSR-7 Streams', (string)$this->streamFactory());
    }
}
