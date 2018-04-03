<?php
/**
 * UriFactory
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Http\Factories;

use Apine\Core\Http\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class UriFactory
 *
 * @package Apine\Core\Http\Factories
 */
class UriFactory
{
    /**
     * Create a new URI.
     *
     * @param string $uri
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException
     *  If the given URI cannot be parsed.
     */
    public function createUri($uri = '') : UriInterface
    {
        return new Uri($uri);
    }
    
    /**
     * Create a new URI from server variables.
     *
     * @param array $server Typically $_SERVER or similar structure.
     *
     * @return \Psr\Http\Message\UriInterface
     * @throws \InvalidArgumentException
     */
    public function createUriFromArray(array $server) : UriInterface
    {
        if (!isset($server['HTTP_HOST']) && !isset($server['SERVER_NAME']) && !isset($server['SERVER_ADDR'])) {
            throw new \InvalidArgumentException("Cannot determine URI from array");
        }
        
        $uri_string = (isset($server['HTTPS']) && !empty($server['HTTPS']) && $server['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $hasQuery = false;
    
    
        if (isset($server['HTTP_HOST'])) {
            $uri_string .= $server['HTTP_HOST'];
        } else {
            if (isset($server['SERVER_NAME'])) {
                $uri_string .= $server['SERVER_NAME'];
            } else if (isset($server['SERVER_ADDR'])) {
                $uri_string .= $server['SERVER_ADDR'];
            }
        
            if (isset($server['SERVER_PORT'])) {
                $uri_string .= ':' . $server['SERVER_PORT'];
            }
        }
    
        if (isset($server['REQUEST_URI'])) {
            $requestParts = explode('?', $server['REQUEST_URI'],2);
        
            if ($requestParts[0] !== "/") {
                $uri_string .= $requestParts[0];
            } else {
                $uri_string = implode('', [$uri_string, $requestParts[0]]);
            }
        
            if (isset($requestParts[1])) {
                $hasQuery = true;
                $uri_string = implode('?', [$uri_string, $requestParts[1]]);
            }
        }
    
        if (!$hasQuery && isset($server['QUERY_STRING'])) {
            $uri_string = implode('?', [$uri_string, $server['QUERY_STRING']]);
        }
    
        return $this->createUri($uri_string);
    }
}