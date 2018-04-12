<?php
/**
 * JSONViewTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Views\JSONView;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class JSONViewTest extends TestCase
{
    
    public function testConstructor()
    {
        $data = [
            'name' => 'value'
        ];
        
        $view = new JsonView($data, true);
        
        $this->assertArrayHasKey('content-type', $this->getObjectAttribute($view, 'headers'));
        $this->assertAttributeNotEmpty('attributes', $view);
        $this->assertAttributeEquals($data, 'attributes', $view);
        $this->assertAttributeEquals(true, 'prettify', $view);
    }
    
    public function testConstructorNoData()
    {
        $view = new JsonView();
        $this->assertAttributeEmpty('attributes', $view);
    }
    
    public function testRespond()
    {
        $data = [
            'name' => 'value',
            'array' => [
                1, 2, 3
            ]
        ];
        
        $view = new JsonView($data);
        $response = $view->respond();
        
        $this->assertInstanceOf(ResponseInterface::class, $response);
        
        $body = $response->getBody();
        $body->rewind();
        $content = $body->getContents();
        
        $this->assertNotEmpty($content);
        $this->assertFalse(strpos($content, PHP_EOL));
    }
    
    public function testRespondPrettify()
    {
        $data = [
            'name' => 'value',
            'array' => [
                1, 2, 3
            ]
        ];
    
        $view = new JsonView($data, true);
        $response = $view->respond();
    
        $body = $response->getBody();
        $body->rewind();
        $content = $body->getContents();
    
        $this->assertNotEmpty($content);
        $this->assertNotFalse(strpos($content, PHP_EOL));
    }
}
