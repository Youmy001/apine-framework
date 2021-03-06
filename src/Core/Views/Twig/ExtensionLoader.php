<?php
/**
 * ExtensionLoader
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Views\Twig;

use Apine\Core\Config;

/**
 * Class ExtensionLoader
 *
 * @package Apine\Core\Views\Twig
 */
class ExtensionLoader
{
    private $environment;
    
    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    
    public function addFromConfig(Config $config) : \Twig_Environment
    {
        if (!isset($config->extensions)) {
            $config->extensions = [
                UriExtension::class,
                ExecutionTimeExtension::class
            ];
            $config->save();
        } else {
            foreach ($config->extensions as $extension) {
                $this->addExtention(new $extension());
            }
        }
        
        return $this->environment;
    }
    
    public function addExtention(\Twig_ExtensionInterface $extension) : \Twig_Environment
    {
        $this->environment->addExtension($extension);
        return $this->environment;
    }
}