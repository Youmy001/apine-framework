<?php
/**
 * ForbiddenException
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Error\Http;

use Throwable;

/**
 * Class ForbiddenException
 *
 * @package Apine\Core\Error\Http
 */
class ForbiddenException extends HttpException
{
    public function __construct(
        string $message = 'Forbidden',
        Throwable $previous = null
    )
    {
        parent::__construct($message, 403, $previous);
    }
}