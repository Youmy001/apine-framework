<?php

declare(strict_types=1);

namespace Apine\Core\Views;

use Apine\Core\Utility\URLHelper;
use Twig_Extension;
use function Apine\Core\Utility\executionTime;
use const Apine\Core\PROTOCOL_DEFAULT;
use const Apine\Core\PROTOCOL_HTTP;
use const Apine\Core\PROTOCOL_HTTPS;

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
            /*new \Twig_SimpleFunction('config', function ($a_section, $a_default = '') {
                try {
                    $config = Application::getInstance()->getConfig();
                    $sections = explode('.', $a_section);
                    $value = $config;
                    
                    foreach ($sections as $section) {
                        $value = $value->$section;
                    }
                    
                    if (empty($value)) {
                        $value = $a_default;
                    }
                } catch (\Exception $e) {
                    $value = $a_default;
                }
                
                return $value;
            }),
            new \Twig_SimpleFunction('translate', function ($section, $key) {
                //return Translator::translate($section, $key);
                try {
                
                } catch (\Exception $e) {
                
                }
                
                return '';
            }),*/
            new \Twig_SimpleFunction('execution_time', function () {
                return executionTime();
            }),
            new \Twig_SimpleFunction('path', function ($path, $protocol = 'default') {
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
                
                return URLHelper::path($path, $protocol);
            }),
            new \Twig_SimpleFunction('resource', function ($path) {
                return URLHelper::resource($path);
            })
        );
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('path', function ($path, $protocol = 'default') {
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
                
                return URLHelper::path($path, $protocol);
            }),
            new \Twig_SimpleFilter('resource', function ($path) {
                return URLHelper::resource($path);
            })/*,
            new \Twig_SimpleFilter('date_format', function ($date, $format) {
                $locale = Translator::getInstance()->translation()->get_locale();
                
                if (is_a($date, '\DateTime')) {
                    $date = $date->getTimestamp();
                } else {
                    if (!is_timestamp($date)) {
                        $date = strtotime($date);
                    }
                }
                
                return $locale->format_date($date, $format);
                
            })*/
        );
    }
}