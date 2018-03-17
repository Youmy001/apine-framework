<?php
/**
 * HTMLView
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Views;

use Apine\Core\Error\ErrorHandler;
use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;


/**
 * Class HTMLView
 *
 * @package Apine\Core\Views
 */
class HTMLView extends View
{
    use InjectableDataTrait;
    
    /**
     * Path to layout file
     *
     * @var string
     */
    private $file;
    
    private $filePath;
    
    public function __construct(string $template, array $data = [], int $code = 200)
    {
        $this->attributes = $data;
        $this->setStatusCode($code);
        $this->setFile($template);
    }
    
    public function respond(): ResponseInterface
    {
        $response = new Response($this->statusCode);
    
        foreach ($this->headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        
        if (!is_null($this->file)) {
            $loader = new Twig_Loader_Filesystem($this->filePath);
            $twig = new Twig_Environment($loader, array(
                'cache'       => 'views/_cache',
                'auto-reload' => true,
                'debug'       => ((bool)ErrorHandler::$reportingLevel)
            ));
            $twig->addExtension(new TwigExtension());
            
            $template = $twig->loadTemplate($this->file);
            $content = $template->render($this->attributes);
            
            $body = new Stream(fopen('php://memory', 'r+'));
            $body->write($content);
            $response = $response->withBody($body);
        }
        
        return $response;
    }
    
    public function setFile(string $path)
    {
        $location = realpath(dirname(__FILE__) . '/../..'); // The path to the framework itself
    
        if (file_exists("views/$path.twig")) {
            $filePath = realpath("views");
            $file = "$path.twig";
        } else if (file_exists("$location/Views/$path.twig")){
            $filePath = "$location/Views";
            $file = "$path.twig";
        } else if (file_exists("$path.twig")) {
            $filePath = $location;
            $file = "$path.twig";
        } else if (file_exists("views/$path.html")) {
            $filePath = realpath("views");
            $file = "$path.html";
        } else if (file_exists("$location/Views/$path.html")){
            $filePath = realpath("$location/Views");
            $file = "$path.html";
        } else if (file_exists("$path.html")) {
            $filePath = $location;
            $file = "$path.html";
        } else {
            throw new \InvalidArgumentException('File not found');
        }
        
        $this->file = $file;
        $this->filePath = $filePath;
    }
}