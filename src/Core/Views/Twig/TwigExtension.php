<?php

declare(strict_types=1);

namespace Apine\Core\Views\Twig;

use Apine\Core\Views\URLHelper;
use Twig_Extension;
use const Apine\Core\PROTOCOL_DEFAULT;
use const Apine\Core\PROTOCOL_HTTP;
use const Apine\Core\PROTOCOL_HTTPS;
use function Apine\Core\Utility\executionTime;

class TwigExtension extends Twig_Extension
{
    public function getName()
    {
        return 'apine';
    }
    
    public function getGlobals()
    {
    
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('execution_time', function () {
                return executionTime();
            }),
            new \Twig_SimpleFunction('path', function ($path, $protocol = 'default') {
                $helper = new URLHelper();
                
                switch ($protocol) {
                    case 'http':
                        $protocol = PROTOCOL_HTTP;
                        break;
                    case 'https':
                        $protocol = PROTOCOL_HTTPS;
                        break;
                    case 'default':
                    default:
                        $protocol = PROTOCOL_DEFAULT;
                        break;
                }
                
                return $helper->path($path, $protocol);
            }),
            new \Twig_SimpleFunction('resource', function ($path) {
                $helper = new URLHelper();
                return $helper->resource($path);
            })
        );
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('path', function ($path, $protocol = 'default') {
                $helper = new URLHelper();
                
                switch ($protocol) {
                    case 'http':
                        $protocol = PROTOCOL_HTTP;
                        break;
                    case 'https':
                        $protocol = PROTOCOL_HTTPS;
                        break;
                    case 'default':
                    default:
                        $protocol = PROTOCOL_DEFAULT;
                        break;
                }
                
                return $helper->path($path, $protocol);
            }),
            new \Twig_SimpleFilter('resource', function ($path) {
                $helper = new URLHelper();
                return $helper->resource($path);
            })
        );
    }
}