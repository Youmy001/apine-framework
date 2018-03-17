<?php
/**
 * View
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Views;

use Psr\Http\Message\ResponseInterface;

/**
 * Class View
 *
 * @package Apine\Core\Views
 */
abstract class View
{
    /**
     * List of HTTP headers to apply
     *
     * @var array
     */
    protected $headers = [];
    
    /**
     * @var int
     */
    protected $statusCode = 200;
    
    /**
     * Produce a response from the view
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    abstract public function respond() : ResponseInterface;
    
    /**
     * Set HTTP status code to return
     * @param int $code
     */
    final function setStatusCode(int $code)
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Invalid HTTP status code');
        }
        
        $this->statusCode = $code;
    }
    
    /**
     * Set the specified header rule
     *
     * @param string $name
     * @param        $value
     */
    final function addHeader(string $name, $value)
    {
        $sanitizedName = strtolower($name);
    
        $this->headers[$sanitizedName] = [
            'name'  => $name,
            'value' => $value
        ];
    }
    
    /**
     * Remove HTTP header from response
     *
     * @param string $name
     */
    final function removeHeader(string $name)
    {
        $sanitizedName = strtolower($name);
        
        unset($this->headers[$sanitizedName]);
    }
}