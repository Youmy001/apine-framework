<?php
/**
 * CookieExtension
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Views\Twig;

/**
 * Class CookieExtension
 *
 * @package Apine\Core\Views\Twig
 */
class CookieExtension extends \Twig_Extension
{
    function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cookie', function(string $name): string {
                return $_COOKIE[$name];
            })
        ];
    }
}