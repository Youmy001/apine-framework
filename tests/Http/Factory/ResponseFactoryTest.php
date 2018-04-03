<?php
/**
 * ResponseFactoryTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Factories\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseFactoryTest extends TestCase
{
    /**
     * @var ResponseFactory
     */
    private $factory;
    
    public function setUp()
    {
        $this->factory = new ResponseFactory();
    }
    
    public function testCreateResponse()
    {
        $response = $this->factory->createResponse(404);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
