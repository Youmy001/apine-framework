<?php
/**
 * UploadedFileFactoryTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Factories\UploadedFileFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactoryTest extends TestCase
{
    /**
     * @var UploadedFileFactory
     */
    private $factory;
    
    public function setUp()
    {
        $this->factory = new UploadedFileFactory();
    
        $fakeresource = fopen('uploadFile','w');
        fwrite($fakeresource, 'Some Content');
        fclose($fakeresource);
    }
    
    public function tearDown()
    {
        unlink('uploadFile');
    }
    
    public function testCreateUploadedFile()
    {
        $resource = fopen('php://memory', 'r+');
        fwrite($resource, 'Some Content');
        
        $uploadedFile = $this->factory->createUploadedFile(
            $resource,
            12,
            \UPLOAD_ERR_OK
        );
        
        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
    }
    
    public function testCreateUploadedFileFromArray()
    {
        $files = [
            "files1" => [
                "name" => "example1",
                "type" => "text/plain",
                "tmp_name" => "uploadedFile",
                "error" => 0,
                "size" => 12
            ],
            "files2" => [
                "name" => [
                    "example2",
                    "example3"
                ],
                "type" => [
                    "text/plain",
                    "text/plain"
                ],
                "tmp_name" => [
                    "uploadedFile",
                    "uploadedFile"
                ],
                "error" => [
                    0,
                    0
                ],
                "size" => [
                    12,
                    12
                ]
            ]
        ];
        
        $uploadedFiles = $this->factory->createUploadedFileFromArray($files);
        $this->assertInternalType('array', $uploadedFiles);
        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles[0]);
    }
}
