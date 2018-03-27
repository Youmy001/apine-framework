<?php
/**
 * ResponseTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testConstructorDefaults()
    {
        $response = new Response();
        $this->assertAttributeEquals(200, 'statusCode', $response);
        $this->assertAttributeEquals([], 'headers', $response);
        $this->assertAttributeInstanceOf(Stream::class, 'body', $response);
    }
    
    public function testConstructorCustom()
    {
        $headers = [
            'Cache-Control' => 'max-age=3600',
            'Content-Language' => 'en'
        ];
        $content = fopen('php://memory', 'r+');
        fwrite($content, 'Sorry but the access to this page is forbidden');
        $body = new Stream($content);
        
        $response = new Response(
            403,
            $headers,
            $body
        );
        
        $this->assertAttributeEquals(403, 'statusCode', $response);
        $this->assertAttributeEquals($headers, 'headers', $response);
        $this->assertAttributeEquals($body, 'body', $response);
    }
    
    public function testConstructorCustomReason()
    {
        $reason = 'Custom Reason';
        
        $response = new Response(
            401,
            [],
            null,
            '1.1',
            $reason
        );
        
        $this->assertAttributeEquals($reason, 'reasonPhrase', $response);
    }
    
    public function testGetStatusCode()
    {
        $this->assertEquals(200, (new Response())->getStatusCode());
    }
    
    public function testWithStatus()
    {
        $response = (new Response())->withStatus(404);
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid HTTP status code
     */
    public function testWithStatusInvalidCode()
    {
        $response = new Response();
        $response->withStatus(600);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A reason phrase must be supplied for this status code
     */
    public function testWithStatusInvalidReason()
    {
        $response = new Response();
        $response->withStatus(419); // 419 is not standard thus requires a reason to be supplied
    }
    
    public function testGetReasonPhrase()
    {
        $response = new Response();
        $this->assertEquals('OK', $response->getReasonPhrase());
    }
}
