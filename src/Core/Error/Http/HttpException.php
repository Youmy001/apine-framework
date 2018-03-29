<?php
/**
 * HttpException
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Error\Http;

use \Exception;
use Throwable;

/**
 * Class HttpException
 *
 * @package Apine\Core\Error\Http
 */
class HttpException extends Exception
{
    public function __construct(
        string $message,
        int $code = 500,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}