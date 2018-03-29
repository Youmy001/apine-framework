<?php
/**
 * UnauthorizedException
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Error\Http;

use Throwable;

/**
 * Class UnauthorizedException
 *
 * @package Apine\Core\Error\Http
 */
class UnauthorizedException extends HttpException
{
    public function __construct(
        string $message = 'Unauthorized',
        Throwable $previous = null
    )
    {
        parent::__construct($message, 401, $previous);
    }
}