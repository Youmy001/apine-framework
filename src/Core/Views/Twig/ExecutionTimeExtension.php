<?php
/**
 * ExcecutionTimeExtension
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Views\Twig;

use function Apine\Core\Utility\executionTime;

/**
 * Class ExcecutionTimeExtension
 *
 * @package Apine\Core\Views\Twig
 */
class ExecutionTimeExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('execution_time', function () {
                return executionTime();
            })
        );
    }
}