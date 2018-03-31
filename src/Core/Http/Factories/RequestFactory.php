<?php
/**
 * RequestFactory
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Http\Factories;

use Apine\Core\Http\Request;
use Apine\Core\Http\Stream;
use Apine\Core\Http\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use const Apine\Core\REQUEST_USER;
use const Apine\Core\REQUEST_MACHINE;

/**
 * Class RequestFactory
 *
 * @package Apine\Core\Http\Factories
 */
class RequestFactory
{
    /**
     * Create a new server request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest($method, $uri) : ServerRequestInterface
    {
        return new Request($method, $uri);
    }
    
    /**
     * Create a new server request from server variables.
     *
     * @param array $server Typically $_SERVER or similar structure.
     *
     * @return ServerRequestInterface
     *
     * @throws \InvalidArgumentException
     *  If no valid method or URI can be determined.
     */
    public function createServerRequestFromArray(array $server) : ServerRequestInterface
    {
        if (!isset($server['REQUEST_METHOD'])) {
            throw new \InvalidArgumentException("Cannot determine valid request method from array");
        }
    
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        
        $uriFactory = new UriFactory();
        $uri = $uriFactory->createUriFromArray($server);
        
        return new Request(
            $server['REQUEST_METHOD'],
            $uri,
            [],
            null,
            $protocol,
            $server
        );
    }
    
    /**
     * Create a new server request representing the
     * incoming request using all the server variables
     *
     * @param array $server     Typically $_SERVER
     * @param array $get        Typically $_GET
     * @param array $post       Typically $_POST
     * @param array $files      Typically $_FILES
     * @param array $cookies    Typically $_COOKIES
     * @param array $headers    List of incoming request headers usually coming from getallheaders()
     * @param null  $body       Body of the incoming request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function createServerRequestFromGlobals(array $server, array $get = [], array $post = [], array $files = [], array $cookies = [], array $headers = [], $body = null) : ServerRequestInterface
    {
        $uriFactory = new UriFactory();
        $uri = $uriFactory->createUriFromArray($server);
    
        $method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET';
        
        $protocol = isset($server['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1';
    
        if (isset($get['apine-request'])) {
            $requestString = $get['apine-request'];
            $requestArray = explode("/", $requestString);
        
            if ($requestArray[1] === 'api') {
                $type = REQUEST_MACHINE;
            } else {
                $type = REQUEST_USER;
            }
        
            unset($get['apine-request']);
        }
        
        $request = new Request(
            $method,
            $uri,
            $headers,
            $body,
            $protocol,
            $_SERVER
        );
        
        return $request
            ->withCookieParams($cookies)
            ->withQueryParams($get)
            ->withParsedBody($post)
            ->withUploadedFiles(self::formatFiles($files))
            ->withAttribute('type', $type);
    }
    
    private static function formatFiles(array $files) : array
    {
        $normalized = [];
        
        foreach ($files as $key => $value) {
            if (is_array($value) && isset($value['name'])) {
                $normalized[$key] = self::createUploadedFile($value);
            } else if (is_array($value)) {
                $normalized[$key] = self::formatFiles($value);
            } else {
                throw new \InvalidArgumentException('Invalid files specification');
            }
            
        }
        
        return $normalized;
    }
    
    private static function createUploadedFile (array $value)
    {
        if (is_array($value['name'])) {
            $normalized = [];
            $files = $value['name'];
            
            foreach (array_keys($files['name']) as $key) {
                $values = [
                    'tmp_name' => $files['tmp_name'][$key],
                    'size' => $files['size'][$key],
                    'error' => $files['error'][$key],
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key]
                ];
                
                $normalized[$key] = self::createUploadedFile($values);
            }
            
            return $files;
        } else {
            return new UploadedFile(
                $value['tmp_name'],
                (int)$value['size'],
                (int)$value['error'],
                $value['name'],
                $value['type']
            );
        }
    }
}