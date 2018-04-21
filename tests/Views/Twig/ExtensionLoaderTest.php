<?php
/**
 * ExtensionLoaderTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Config;
use Apine\Core\Views\Twig\ExtensionLoader;
use PHPUnit\Framework\TestCase;

class ExtensionLoaderTest extends TestCase
{
    /**
     * @var ExtensionLoader
     */
    private $loader;
    
    public function deleteConfig() {
    
    }
    
    public function testConstructor()
    {
        $twigloader = $this->getMockBuilder(Twig_LoaderInterface::class)->getMock();
        
        $observer = $this->getMockBuilder(Twig_Environment::class)
            ->setConstructorArgs([
                'loader' => $twigloader
            ])
            ->setMethods(['addExtension'])
            ->getMock();
        
        $this->loader = new ExtensionLoader($observer);
        
        $this->assertAttributeInstanceOf(Twig_Environment::class, 'environment', $this->loader);
    }
    
    public function testAddFromConfig()
    {
        
        $resource = fopen('twig.json', 'w+');
        fwrite($resource, json_encode([
            'extensions' => [
                StubExtension::class
            ]
        ]));
        fclose($resource);
        
        $twigloader = $this->getMockBuilder(Twig_LoaderInterface::class)->getMock();
    
        $observer = $this->getMockBuilder(Twig_Environment::class)
            ->setConstructorArgs([
                'loader' => $twigloader
            ])
            ->setMethods(['addExtension'])
            ->getMock();
    
        $loader = new ExtensionLoader($observer);
        
        $twig = $loader->addFromConfig(new Config('twig.json'));
        $this->assertInstanceOf(Twig_Environment::class, $twig);
        
        unlink('twig.json');
    }
    
    public function testAddFromConfigConfigNotExists()
    {
        $this->assertFileNotExists('twig.json');
        
        $twigloader = $this->getMockBuilder(Twig_LoaderInterface::class)->getMock();
    
        $observer = $this->getMockBuilder(Twig_Environment::class)
            ->setConstructorArgs([
                'loader' => $twigloader
            ])
            ->setMethods(['addExtension'])
            ->getMock();
    
        $loader = new ExtensionLoader($observer);
        
        $loader->addFromConfig(new Config('twig.json'));
        $this->assertFileExists('twig.json');
    
        unlink('twig.json');
    }
    
    public function testAddExtention()
    {
        $twigloader = $this->getMockBuilder(Twig_LoaderInterface::class)->getMock();
    
        $environment = $this->getMockBuilder(Twig_Environment::class)
            ->setConstructorArgs([
                'loader' => $twigloader
            ])
            ->setMethods(['addExtension'])
            ->getMock();
        
        $extension = $this->getMockBuilder(Twig_Extension::class)->getMock();
    
        $loader = new ExtensionLoader($environment);
    
        $twig = $loader->addExtention($extension);
        $this->assertInstanceOf(Twig_Environment::class, $twig);
    }
}

class StubExtension extends Twig_Extension {}
