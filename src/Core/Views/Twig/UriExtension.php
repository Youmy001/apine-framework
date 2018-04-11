<?php

declare(strict_types=1);

namespace Apine\Core\Views\Twig;

use Apine\Core\Views\URLHelper;
use Twig_Extension;
use const Apine\Core\PROTOCOL_DEFAULT;
use const Apine\Core\PROTOCOL_HTTP;
use const Apine\Core\PROTOCOL_HTTPS;

class UriExtension extends Twig_Extension
{
    /**
     * @var URLHelper
     */
    private $urlHelper;
    
    public function __construct()
    {
        $this->urlHelper = new URLHelper();
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('path', function (string $path, string $protocol = 'default') {
                return $this->urlHelper->path($path, $this->getProtocol($protocol));
            }),
            new \Twig_SimpleFunction('resource', function ($path) {
                return $this->urlHelper->resource($path);
            })
        );
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('path', function (string $path, string $protocol = 'default') {
                return $this->urlHelper->path($path, $this->getProtocol($protocol));
            }),
            new \Twig_SimpleFilter('resource', function ($path) {
                return $this->urlHelper->resource($path);
            })
        );
    }
    
    private function getProtocol(string $protocol) : int
    {
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
        
        return $protocol;
    }
}