<?php
/**
 * DependencyResolverTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

use Apine\Core\Container\Container;
use Apine\Core\Controllers\Controller;
use Apine\Core\Config;
use Apine\Core\Http\Uri;
use Apine\Core\Http\Request;
use Apine\Core\Http\Response;
use Apine\Core\Routing\DependencyResolver;
use Apine\Core\Routing\Parameter;
use Apine\Core\Routing\Route;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class DependencyResolverTest extends TestCase
{
    private static function requestFactory ()
    {
        return (new Request(
            'GET',
            new Uri('https://example.com/156?home=cat'),
            [],
            null,
            '1.1',
            $_SERVER
        ));
    }
    
    private static function routeFactory()
    {
        return new Route(
            'GET',
            '/{input}',
            TestDependencyController::class,
            'inputTest'
        );
    }
    
    private static function containerFactory()
    {
        $container = new Container();
    
        $container->register(Request::class, function () : Request {
            return self::requestFactory();
        });
    
        $container->register(Response::class, function () : Response {
            return new Response();
        }, true);
    
        return $container;
    }
    
    public function testResolveWebParameters()
    {
        $request = self::requestFactory();
        $route = self::routeFactory();
    
        $parameters = DependencyResolver::resolveWebParameters($request, $route);
        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('input', $parameters);
    }
    
    public function testResolveAPIParameters()
    {
        $request = self::requestFactory();
        
        $parameters = DependencyResolver::resolveAPIParameters($request);
        $this->assertInternalType('array', $parameters);
        $this->assertArrayHasKey('home', $parameters);
    }
    
    public function testMapParametersForRequest()
    {
        $request = self::requestFactory();
        $route = self::routeFactory();
        
        $parameters = DependencyResolver::mapParametersForRequest($request, $route);
        
        $this->assertInternalType('array', $parameters);
    }
    
    public function testGetContainerServiceForParam()
    {
        $container = self::containerFactory();
        $parameter = new Parameter(
            Request::class,
            'request'
        );
        
        $this->assertInstanceOf(
            Request::class,
            DependencyResolver::getContainerServiceForParam($container, $parameter)
        );
    }
    
    public function testGetContainerServiceForParamNotAService()
    {
        $container = self::containerFactory();
        $parameter = new Parameter(
            DOMDocument::class,
            'document'
        );
    
        $this->assertNull(
            DependencyResolver::getContainerServiceForParam($container, $parameter)
        );
    }
    
    public function testMapConstructorArguments()
    {
        $container = self::containerFactory();
        $route = self::routeFactory();
    
        $reflection = new \ReflectionClass($route->controller);
        $constructor = $reflection->getConstructor();
    
        $arguments = DependencyResolver::mapConstructorArguments($container, $constructor->getParameters());
        $this->assertInternalType('array', $arguments);
    }
    
    public function testMapActionArguments()
    {
        $container = self::containerFactory();
        $route = self::routeFactory();
        $request = self::requestFactory();
        
        $requestParams = DependencyResolver::mapParametersForRequest($request, $route);
        
        $map = DependencyResolver::mapActionArguments($container, $requestParams, $route->actionParameters);
        $this->assertInternalType('array', $map);
    }
}

class TestDependencyController extends Controller {
    public function __construct(RequestInterface $request, Config $config) {}
    public function inputTest(int $input){}
    public function inputTestTwo(string $first, int $second){}
}