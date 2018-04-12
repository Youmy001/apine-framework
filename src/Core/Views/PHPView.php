<?php
/**
 * PHPView
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
 * Class PHPView
 *
 * @package Apine\Core\Views
 */
class PHPView extends View
{
    use InjectableDataTrait;
    
    /**
     * Path to layout file
     *
     * @var string
     */
    private $file;
    
    public function __construct(string $template, array $data = [], int $code = 200)
    {
        $this->attributes = $data;
        $this->setFile($template);
        $this->setStatusCode($code);
    }
    
    public function respond(): ResponseInterface
    {
    
        $response = new Response($this->statusCode);
        
        if (!is_null($this->file)) {
            $body = new Stream(fopen('php://memory', 'r+'));
            $body->write($this::generate($this->file, $this->attributes));
            $response = $response->withBody($body);
        }
        
        foreach ($this->headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        
        return $response;
    }
    
    public function setFile(string $path)
    {
        if (file_exists("$path.php")) {
            $path = "$path.php";
        } else {
            throw new \InvalidArgumentException('File not found');
        }
        
        $this->file = $path;
    }
    
    private static function generate (string $layoutFile, array $layoutAttributes) : string
    {
        try {
            extract($layoutAttributes);
            unset($layoutAttributes);
        
            ob_start();
            eval("unset(\$layoutFile); include_once('".$layoutFile."');");
            $content = ob_get_contents();
            ob_end_clean();
            
            return $content;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }
}