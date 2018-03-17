<?php
/**
 * ViewTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Views\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @var View
     */
    private $object;
    
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(View::class);
    }
    
    public function test_respond()
    {
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, ($this->object)());
    }
    
    public function testSetStatusCode()
    {
        $this->object->setStatusCode(500);
        $this->assertAttributeEquals(500, 'statusCode', $this->object);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStatusCodeTooSmall()
    {
        $this->object->setStatusCode(80);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStatusCodeTooLarge()
    {
        $this->object->setStatusCode(8080);
    }
    
    public function testAddHeader()
    {
        $this->object->addHeader('Content-Type', 'text/css');
        $this->assertArrayHasKey('content-type', $this->getObjectAttribute($this->object, 'headers'));
    }
    
    public function testRemoveHeader()
    {
        $this->object->removeHeader('Content-Type');
        $this->assertArrayNotHasKey('content-type', $this->getObjectAttribute($this->object, 'headers'));
    }
}
