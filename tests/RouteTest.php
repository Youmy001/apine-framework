<?php
/**
 * RouteTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\MVC\Controller;
use Apine\Core\Routing\Parameter;
use Apine\Core\Routing\ParameterDefinition;
use Apine\Core\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    
    public function testInvalidController()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Controller or method not found');
        
        $route = new Route(
            'GET',
            '/',
            NotController::class,
            'index'
        );
    }
    
    public function testInvalidAction()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Controller or method not found');
        
        $route = new Route(
            'GET',
            '/',
            TestController::class,
            'fake'
        );
    }
    
    public function testParameterDefinition()
    {
        $method = "GET";
        $pattern = "/test/{input}";
        $controller = TestController::class;
        $action = "inputTest";
        $parameters = [
            [
                'name' => 'input',
                'regex' => '([0-9]+)'
            ]
        ];
    
        $route = new Route($method, $pattern, $controller, $action, $parameters);
        
        $this->assertEquals(
            [
                new ParameterDefinition('input', '([0-9]+)')
            ],
            $route->parameters
        );
    }
    
    /**
     * @depends testInvalidController
     * @depends testInvalidAction
     * @depends testParameterDefinition
     */
    public function testConstructor()
    {
        $method = "GET";
        $pattern = "/test/{input}";
        $controller = TestController::class;
        $action = "inputTest";
        
        $route = new Route($method, $pattern, $controller, $action);
        
        $this->assertAttributeEquals($method, 'method', $route);
        $this->assertAttributeEquals($pattern, 'uri', $route);
        $this->assertAttributeEquals($controller, 'controller', $route);
        $this->assertAttributeEquals($action, 'action', $route);
        $this->assertEquals(
            [
                new ParameterDefinition('input', '(.+?)')
            ],
            $route->parameters
        );
        $this->assertEquals(
            [
                new Parameter('int', 'input')
            ],
            $route->actionParameters
        );
    }
    
    public function testMatch()
    {
        $routeOne = new Route(
            'GET',
            '/{input}',
            TestController::class,
            'inputTest'
        );
        $routeTwo = new Route(
            'GET',
            '/{input}',
            TestController::class,
            'inputTest',
            [
                [
                    'name' => 'input',
                    'regex' => '([0-9]+)'
                ]
            ]
        );
        $routeThree = new Route(
            'POST',
            '/test/{first}/{second}',
            TestController::class,
            'inputTestTwo'
        );
        $routeFour = new Route(
            'POST',
            '/test/{first}/{second}',
            TestController::class,
            'inputTestTwo',
            [
                [
                    'name' => 'first',
                    'regex' => '(\w+)'
                ],
                [
                    'name' => 'second',
                    'regex' => '([0-9])+'
                ]
            ]
        );
        
        $this->assertTrue($routeOne->match('/15','GET'));
        $this->assertFalse($routeOne->match('/','GET'));
        $this->assertTrue($routeOne->match('/as','GET'));
        $this->assertFalse($routeOne->match('/15','POST'));
    
        $this->assertTrue($routeTwo->match('/15','GET'));
        $this->assertFalse($routeTwo->match('/','GET'));
        $this->assertFalse($routeTwo->match('/as','GET'));
        $this->assertFalse($routeTwo->match('/15','POST'));
        
        $this->assertTrue($routeThree->match('/test/as/15', 'POST'));
        $this->assertFalse($routeThree->match('/test/as/15', 'GET'));
        $this->assertFalse($routeThree->match('/test/15', 'POST'));
        $this->assertFalse($routeThree->match('/test', 'POST'));
        $this->assertFalse($routeThree->match('/', 'POST'));
    
        $this->assertTrue($routeFour->match('/test/as/15', 'POST'));
        $this->assertFalse($routeFour->match('/test/テスト/15', 'POST'));
        $this->assertFalse($routeFour->match('/test/as/no', 'POST'));
    }
}

class TestController extends Controller {
    public function inputTest(int $input){}
    public function inputTestTwo(string $first, int $second){}
}

class NotController{}