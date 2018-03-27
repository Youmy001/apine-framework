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
     * @covers UploadedFile::getClientFilename()
     * @covers UploadedFile::getClientMediaType()
     * @covers UploadedFile::getError()
     * @covers UploadedFile::getSize()
     *
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
    
    /**
     * @covers UploadedFile::getStream()
     * @depends testConstructor
     * @param UploadedFile $uploadedFile
     */
    public function testGetStream(UploadedFile $uploadedFile)
    {
        $this->assertInstanceOf(Stream::class, $uploadedFile->getStream());
    }
    
    /**
     * @covers UploadedFile::moveTo()
     * @depends testConstructor
     * @param UploadedFile $uploadedFile
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /No such file or directory/
     */
    public function testMoveToCannotWrite(UploadedFile $uploadedFile)
    {
        $newName = './weirdDirectory/' . uniqid('test-');
        $uploadedFile->moveTo($newName);
    }
    
    /**
     * @covers UploadedFile::moveTo()
     * @depends testConstructor
     * @param UploadedFile $uploadedFile
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
     * @covers UploadedFile::moveTo()
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
     * @covers UploadedFile::moveTo()
     * @uses UploadedFile::getStream()
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