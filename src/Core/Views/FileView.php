<?php
/**
 * FileView
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Views;

use Apine\Core\Http\Response;
use Apine\Core\Http\Stream;
use Apine\Utility\Files;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FileView
 *
 * @package Apine\Core\Views
 */
class FileView extends View
{
    /**
     * @var resource
     */
    private $resource;
    
    /**
     * @var string
     */
    private $path;
    
    /**
     * FileView constructor.
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->setFile($file);
    }
    
    public function respond(): ResponseInterface
    {
        $response = new Response($this->statusCode);
    
        foreach ($this->headers as $header) {
            $response = $response->withHeader($header['name'], $header['value']);
        }
        
        $response = $response->withBody(new Stream($this->resource));
        
        return $response;
    }
    
    public function setFile(string $file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('File %s not found',$file));
        }
            
        $this->resource = fopen($file, 'r');
        $this->path = $file;
        
        $this->addHeader('Content-Type', self::fileType($this->path));
        $this->addHeader('Content-Length', filesize($this->path));
    }
    
    private static function fileType(string $path) : string
    {
        $mime = '';
    
        if (class_exists('finfo')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
        } elseif (!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filename = escapeshellcmd($path);
            $mime = shell_exec("file -b --mime-type '" . $filename . "'");
        } elseif (self::isExecAvailable()) {
            $filename = escapeshellcmd($path);
            $mime = exec("file -b --mime-type '" . $filename . "'");
        }
    
        return $mime;
    }
    
    /**
     * Verify if exec is disabled
     *
     * @author Daniel Convissor
     * @see http://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
     */
    private static function isExecAvailable()
    {
        static $available;
        
        if (!isset($available)) {
            $available = true;
            if (ini_get('safe_mode')) {
                $available = false;
            } else {
                $d = ini_get('disable_functions');
                $s = ini_get('suhosin.executor.func.blacklist');
                if ("$d$s") {
                    $array = preg_split('/,\s*/', "$d,$s");
                    if (in_array('exec', $array)) {
                        $available = false;
                    }
                }
            }
        }
        
        return $available;
    }
}