<?php
/**
 * ResponseFactory
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Http\Factories;

use Apine\Core\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseFactory
 *
 * @package Apine\Core\Http\Factories
 */
class ResponseFactory
{
    /**
     * Create a new response.
     *
     * @param int $code HTTP status code
     *
     * @return ResponseInterface
     */
    public function createResponse($code = 200) : ResponseInterface
    {
        return new Response($code);
    }
}