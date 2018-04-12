<?php
/**
 * JSONView
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Views;

use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Psr\Http\Message\ResponseInterface;


/**
 * Class JSONView
 *
 * @package Apine\Core\Views
 */
class JSONView extends View
{
    use InjectableDataTrait;
    
    private $prettify = false;
    
    public function __construct(array $data = [], bool $prettify = false)
    {
        $this->prettify = $prettify;
        
        $this->setAttributes($data);
        $this->addHeader('Content-type', 'application/json');
    }
    
    public function respond(): ResponseInterface
    {
        $response = new Response($this->statusCode);
        $options = 0;
    
        if (true === $this->prettify){
            $options |= JSON_PRETTY_PRINT;
        }
    
        $json = json_encode($this->getAttributes(), $options);
    
        foreach ($this->headers as $header) {
            $response = $response->withHeader($header['name'], $header['value']);
        }
    
        $body = new Stream(fopen('php://memory', 'r+'));
        $body->write($json);
        $response = $response->withBody($body);
        
        return $response;
    }
}