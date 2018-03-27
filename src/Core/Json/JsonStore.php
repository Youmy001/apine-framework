<?php
/**
 * Json Storage
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Json;

class JsonStore
{
    private static $instance;
    
    /**
     * @var Json[]
     */
    private $jsons = [];
    
    static function getInstance() : self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    
    /**
     * @param string $path
     *
     * @return mixed
     * @throws \RuntimeException
     * @throws JsonStoreFileNotFoundException
     */
    static function &get(string $path)
    {
        if (false === ($realPath = realpath($path))) {
            $realPath = realpath('') . DIRECTORY_SEPARATOR . $path;
        }
    
        if (!isset(self::getInstance()->jsons[$realPath])) {
            if (file_exists($path)) {
                $json = new Json(file_get_contents($realPath));
                self::getInstance()->jsons[$realPath] = $json;
            } else {
                throw new JsonStoreFileNotFoundException('File not found');
            }
        }
    
        return self::getInstance()->jsons[$realPath];
    }
    
    /**
     * Replace or create of a json file
     *
     * @param string $path
     * @param mixed|Json  $content
     *
     * @throws \Exception
     */
    static function set(string $path, $content)
    {
        $globalPath = realpath('');
        
        if (!$content instanceof Json) {
            $content = new Json($content);
        }
        
        if (file_exists($path)) {
            $realPath = realpath($path);
        } else {
            $realPath = $globalPath . DIRECTORY_SEPARATOR . $path;
        }
        
        self::getInstance()->jsons[$realPath] = $content;
    }
    
    /**
     * JsonStore destructor
     *
     * Save the content of opened files at the end of the execution
     */
    function __destruct()
    {
        foreach (self::getInstance()->jsons as $path => $json) {
            self::write($path, $json);
        }
        
        self::getInstance()->jsons = [];
    }
    
    /**
     * Write the content on a file
     *
     * @param string $path
     * @param Json   $content
     */
    private static function write(string $path, $content)
    {
        $resource = fopen($path, 'c+');
        fwrite($resource, (string) $content);
        fclose($resource);
    }
}