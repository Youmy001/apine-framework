<?php
/**
 * NotFoundException
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Error\Http;

use Throwable;

/**
 * Class NotFoundException
 *
 * @package Apine\Core\Error\Http
 */
class NotFoundException extends HttpException
{
    public function __construct(
        string $message = 'Not Found',
        Throwable $previous = null
    )
    {
        parent::__construct($message, 404, $previous);
    }
}