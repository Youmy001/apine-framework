<?php
/**
 * Custom Twig Extension
 *
 * @licence MIT
 * @copyright 2016-2017 Tommy Teasdale
 */
namespace Apine\MVC\Twig;

use Apine\MVC\HTMLView;
use Apine\Session\SessionManager;
use Apine\Application\Config;
use Apine\Application\Translator;
use Apine\MVC\URLHelper;
use Twig_Extension;

class TwigExtension extends Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    public function getName()
    {
        return 'apine';
    }

    public function getFunctions() {
        return array(
        	new \Twig_SimpleFunction('config', function ($section, $key) {
        		return Config::get($section, $key);
			}),
            new \Twig_SimpleFunction('translate', function($section, $key) {
                return Translator::translate($section, $key);
            }),
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
			new \Twig_SimpleFunction('resource', function($path) {
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
			}),
			new \Twig_SimpleFilter('date_format', function ($date, $format) {
				$locale = Translator::get_instance()->translation()->get_locale();
				
				if (is_a($date, '\DateTime')) {
					$date = $date->getTimestamp();
				} else if (!is_timestamp($date)) {
					$date = strtotime($date);
				}
				
				return $locale->format_date($date, $format);
				
			})
		);
	}
}