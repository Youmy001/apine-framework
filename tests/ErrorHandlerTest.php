<?php
/**
 * ErrorHandlerTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Error\ErrorHandler;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    protected function setUp()
    {
        //$this->markTestSkipped();
    }
    
    public function testSet()
    {
        ErrorHandler::set(E_NOTICE, true);
        $this->assertEquals(E_NOTICE, ErrorHandler::$reportingLevel);
        $this->assertEquals(true, ErrorHandler::$showTrace);
    }
    
    /**
     * @depends testSet
     */
    public function testUnset()
    {
        ErrorHandler::unset();
        $this->assertEquals(E_ALL, ErrorHandler::$reportingLevel);
        $this->assertEquals(false, ErrorHandler::$showTrace);
    }
    
    public function testHandleError()
    {
        try {
            ErrorHandler::handleError(E_USER_ERROR, 'Test Error', __FILE__, 40);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ErrorException::class, $e);
            $this->assertAttributeEquals(E_USER_ERROR, 'severity', $e);
        }
    }
    
    public function testHandleException()
    {
        try {
            ErrorHandler::handleError(E_USER_ERROR, 'Test Error', __FILE__, 40);
        } catch (\Throwable $e) {
            ob_start();
            ErrorHandler::handleException($e);
            $content = ob_get_contents();
            ob_end_clean();
            
            $this->assertTrue(headers_sent());
            $this->assertInternalType('string', $content);
            $this->assertStringStartsWith('Test Error', $content);
        }
    }
}
