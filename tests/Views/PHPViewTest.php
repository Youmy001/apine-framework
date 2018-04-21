<?php
/**
 * PHPViewTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Error\ErrorHandler;
use Apine\Core\Views\PHPView;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class PHPViewTest extends TestCase
{
    private static $filename = 'response';
    
    public function setUp()
    {
        ErrorHandler::set(E_ALL); // Set up error handling so that notice in file manipulation convert to Exception
    }
    
    public function tearDown()
    {
        ErrorHandler::unset();
    }
    
    /**
     * @beforeClass
     */
    public static function createFile()
    {
        $resource = fopen(self::$filename . '.php', 'w+');
        fwrite($resource, '<h1>Header generated from PHP</h1>');
        fclose($resource);
    }
    
    /**
     * @afterClass
     */
    public static function deleteFile()
    {
        unlink(self::$filename . '.php');
        
        if (file_exists(self::$filename . '2.php')) {
            unlink(self::$filename . '2.php');
        }
    }
    
    public function testConstructor()
    {
        $view = new PHPView(self::$filename);
    
        $this->assertAttributeInternalType('string', 'file', $view);
        $this->assertAttributeEquals(self::$filename . '.php', 'file', $view);
    }
    
    public function testConstructorCustomStatusCode()
    {
        $view = new PHPView(self::$filename, [], 340);
        
        $this->assertAttributeInternalType('integer', 'statusCode', $view);
        $this->assertAttributeEquals(340, 'statusCode', $view);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorCustomStatusCodeInvalid()
    {
        $view = new PHPView(self::$filename, [], 640);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found
     */
    public function testConstructorTemplateFileNotFound()
    {
        $view = new PHPView('notfound');
    }
    
    public function testConstructorData()
    {
        $view = new PHPView(self::$filename, ['name' => 'value']);
        $this->assertAttributeNotEmpty('attributes', $view);
    }
    
    public function testSetFile()
    {
        $view = new PHPView(self::$filename);
        $view->setFile(self::$filename);
    
        $this->assertAttributeEquals(self::$filename . '.php', 'file', $view);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found
     */
    public function testSetFileNotFound()
    {
        $view = new PHPView(self::$filename);
        $view->setFile('notfound');
    }
    
    public function testRespond()
    {
        $view = new PHPView(self::$filename);
        $view->addHeader('fake', 'value');
        $response = $view->respond();
    
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertTrue($response->hasHeader('fake'));
    }
    
    /**
     * @expectedException \Throwable
     */
    public function testRespondErrorWhileGeneratingTheBody()
    {
        $resource = fopen(self::$filename . '2.php', 'w+');
        fwrite($resource, '<h1>Header generated from PHP<?php echo $dog;?></h1>');
        fclose($resource);
        
        $view = new PHPView(self::$filename.'2');
        $response = $view->respond();
    }
}
