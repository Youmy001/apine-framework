<?php

declare(strict_types=1);

namespace Apine\Core\Views;

use Apine\Application\Application;
use Apine\MVC\HTMLView;
use Apine\Session\SessionManager;
use Apine\Application\Config;
use Apine\Application\Translator;
use Apine\Core\Utility\URLHelper;
use Twig_Extension;

class TwigExtension extends Twig_Extension
{
    public function getName()
    {
        return 'apine';
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
                return apine_execution_time();
            }),
            new \Twig_SimpleFunction('path', function ($path, $protocol = 'default') {
                switch ($protocol) {
                    case 'http':
                        $protocol = APINE_PROTOCOL_HTTP;
                        break;
                    case 'https':
                        $protocol = APINE_PROTOCOL_HTTPS;
                        break;
                    case 'default':
                    default:
                        $protocol = APINE_PROTOCOL_DEFAULT;
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
                        $protocol = APINE_PROTOCOL_HTTP;
                        break;
                    case 'https':
                        $protocol = APINE_PROTOCOL_HTTPS;
                        break;
                    case 'default':
                    default:
                        $protocol = APINE_PROTOCOL_DEFAULT;
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