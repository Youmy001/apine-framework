<?php
/**
 * BadRequestException
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Error\Http;

use Throwable;

/**
 * Class BadRequestException
 *
 * @package Apine\Core\Error\Http
 */
class BadRequestException extends HttpException
{
    public function __construct(
        string $message = 'Bad Request',
        Throwable $previous = null
    )
    {
        parent::__construct($message, 400, $previous);
    }
}