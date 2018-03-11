<?php
/**
 * DependencyResolverTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Http\Uri;
use Apine\Core\Http\Request;
use Apine\Core\Routing\DependencyResolver;
use PHPUnit\Framework\TestCase;

class DependencyResolverTest extends TestCase
{
    private static function requestFactory ()
    {
        return (new Request(
            'GET',
            new Uri('https://example.com/test/as/15'),
            [],
            null,
            '1.1',
            $_SERVER
        ))->withQueryParams([
            'apine-request' => '/test/as/15'
        ]);
    }
    
    public function testMapParametersForRequest()
    {
        $request = self::requestFactory();
        $this->assertTrue(true);
    }
    
    /*public function testResolveWebParameters()
    {
    
    }
    
    public function testResolveAPIParameters()
    {
    
    }
    
    public function testMapConstructorArguments()
    {
    
    }
    
    public function testMapActionArguments()
    {
    
    }
    
    public function testGetContainerServiceForParam()
    {
    
    }*/
}
