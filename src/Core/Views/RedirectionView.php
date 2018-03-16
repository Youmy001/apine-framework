<?php
/**
 * RedirectionView
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


namespace Apine\Core\Views;

use Apine\Core\Http\Request;
use Apine\Core\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;


/**
 * Class RedirectionView
 *
 * @package Apine\Core\Views
 */
class RedirectionView extends View
{
    /**
     * @var \Psr\Http\Message\UriInterface
     */
    private $uri;
    
    /**
     * RedirectionView constructor.
     *
     * @param UriInterface $uri
     */
    public function __construct(UriInterface $uri, int $code = 301)
    {
        $this->uri = $uri;
        $this->statusCode = $code;
    }
    
    public static function createFromPath(string $path, int $code = 301) : RedirectionView
    {
        $request = Request::createFromGlobals();
        $uri = $request->getUri();
        return new static($uri->withPath($path), $code);
    }
    
    public function __invoke(): ResponseInterface
    {
        $response = new Response($this->statusCode);
    
        foreach ($this->headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        
        return $response->withHeader('Location', (string) $this->uri);
    }
}