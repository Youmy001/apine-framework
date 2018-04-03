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
     * @var UploadedFileFactory
     */
    private $fileFactory;
    
    /**
     * @var UriFactory
     */
    private $uriFactory;
    
    public function __construct()
    {
        $this->uriFactory = new UriFactory();
        $this->fileFactory = new UploadedFileFactory();
    }
    
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
        
        $uri = $this->uriFactory->createUriFromArray($server);
        
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
        $uri = $this->uriFactory->createUriFromArray($server);
    
        $method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET';
        
        $protocol = isset($server['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1';
    
        unset($get['apine-request']);
        
        $uploadedFiles = $this->fileFactory->createUploadedFileFromArray($files);
        
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
            ->withUploadedFiles($uploadedFiles);
    }
}