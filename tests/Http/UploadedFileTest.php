<?php
/**
 * UploadedFileTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Error\ErrorHandler;
use Apine\Core\Http\Stream;
use Apine\Core\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
    static private $filename = './testUploadFile';
    
    public function setUp()
    {
        ErrorHandler::set(); // Set up error handling so that notice in file manipulation convert to Exception
    }
    
    public function tearDown()
    {
        ErrorHandler::unset();
    }
    
    /**
     * @beforeClass
     */
    public static function createTestFile()
    {
        $resource = fopen(self::$filename, 'w');
        $return = fwrite($resource, 'Test Content');
        fclose($resource);
    }
    
    /**
     * @afterClass
     */
    public static function deleteTestFile()
    {
        unlink(self::$filename);
    }
    
    /**
     * @return UploadedFile
     */
    public function testConstructor() : UploadedFile
    {
        $uploadedFile = new UploadedFile(
            self::$filename,
            12,
            0,
            'uploaded.txt',
            'text/plain'
        );
        
        $this->assertEquals('uploaded.txt', $uploadedFile->getClientFilename());
        $this->assertEquals('text/plain', $uploadedFile->getClientMediaType());
        $this->assertEquals(12, $uploadedFile->getSize());
        $this->assertEquals(0, $uploadedFile->getError());
        
        return $uploadedFile;
    }
    
    public function testConstructorFromResource()
    {
        $resource = fopen('php://memory', 'r+');
        
        $uploadedFile = new UploadedFile(
            $resource,
            0,
            0
        );
    
        $this->assertEquals(null, $uploadedFile->getClientFilename());
        $this->assertEquals(null, $uploadedFile->getClientMediaType());
        $this->assertEquals(0, $uploadedFile->getSize());
        $this->assertEquals(0, $uploadedFile->getError());
    }
    
    public function testConstructorFromStream() : UploadedFile
    {
        $resource = fopen('php://memory', 'r+');
        $stream = new Stream($resource);
        
        $uploadedFile = new UploadedFile(
            $stream,
            0,
            0
        );
        
        $this->assertEquals(null, $uploadedFile->getClientFilename());
        $this->assertEquals(null, $uploadedFile->getClientMediaType());
        $this->assertEquals(0, $uploadedFile->getSize());
        $this->assertEquals(0, $uploadedFile->getError());
        
        return $uploadedFile;
    }
    
    public function testConstructorFromSAPI() : UploadedFile
    {
        $uploadedFile = new UploadedFile(
            self::$filename,
            12,
            0,
            'uploaded.txt',
            'text/plain',
            true
        );
    
        $this->assertAttributeEquals(true, 'sapi', $uploadedFile);
        
        return $uploadedFile;
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid resource provided
     */
    public function testConstructorInvalidResource()
    {
        $uploadedFile = new UploadedFile(
            false,
            0,
            0
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Uploaded file filename must be string
     */
    public function testConstructorInvalidFilenameType()
    {
        $resource = fopen('php://memory', 'r+');
        
        $uploadedFile = new UploadedFile(
            $resource,
            0,
            0,
            false
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Uploaded file media type must be string
     */
    public function testConstructorInvalidMediaType()
    {
        $resource = fopen('php://memory', 'r+');
        
        $uploadedFile = new UploadedFile(
            $resource,
            0,
            0,
            null,
            false
        );
    }
    
    /**
     * @depends testConstructor
     * @param UploadedFile $uploadedFile
     */
    public function testGetStream(UploadedFile $uploadedFile)
    {
        $this->assertInstanceOf(Stream::class, $uploadedFile->getStream());
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot retrieve stream due to upload error
     */
    public function testGetStreamOnInvalidUpload()
    {
        $resource = fopen('php://memory', 'r+');
    
        $uploadedFile = new UploadedFile(
            $resource,
            0,
            1
        );
    
        $uploadedFile->getStream();
    }
    
    /**
     * @depends testConstructor
     * @param UploadedFile $uploadedFile
     *
     * @expectedException \ErrorException
     * @expectedExceptionMessageRegExp /No such file or directory/
     */
    public function testMoveToCannotWrite(UploadedFile $uploadedFile)
    {
        $newName = './weirdDirectory/' . uniqid('test-');
        $uploadedFile->moveTo($newName);
    }
    
    /**
     * @depends testConstructor
     * @param UploadedFile $uploadedFile
     * @return UploadedFile
     */
    public function testMoveTo(UploadedFile $uploadedFile)
    {
        $newName = './' . uniqid('test-');
        $uploadedFile->moveTo($newName);
        
        $this->assertFileExists($newName);
        
        unlink($newName);
        self::createTestFile();
        
        return $uploadedFile;
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The specified path is invalid
     */
    public function testMoveToInvalidTarget()
    {
        $resource = fopen('php://memory', 'r+');
    
        $uploadedFile = new UploadedFile(
            $resource,
            0,
            0
        );
        
        $uploadedFile->moveTo(false);
    }
    
    /**
     * @depends testConstructorFromStream
     * @param UploadedFile $uploadedFile
     */
    public function testMoveToStream(UploadedFile $uploadedFile)
    {
        $newName = './' . uniqid('test-');
        $uploadedFile->moveTo($newName);
        
        
        $this->assertFileExists($newName);
    
        unlink($newName);
        self::createTestFile();
    }
    
    /**
     * @depends testMoveTo
     * @param UploadedFile $uploadedFile
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File has already been moved once
     */
    public function testMoveToCannotDoTwice(UploadedFile $uploadedFile)
    {
        $newName = './' . uniqid('test-');
        $uploadedFile->moveTo($newName);
    }
    
    /**
     * @depends testMoveTo
     *
     * @param UploadedFile $uploadedFile
     *
     * @expectedException \RuntimeException
     */
    public function testGetStreamOnMovedStream(UploadedFile $uploadedFile)
    {
        $uploadedFile->getStream();
    }
}