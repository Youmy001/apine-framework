<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 17/01/14
 * Time: 2:16
 */
declare(strict_types=1);

namespace Apine\Core;


class JsonStore
{
    private static $instance;
    
    /**
     * @var array
     */
    private $jsons;
    
    static function getInstance() : self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     * @param string $a_path
     *
     * @return mixed
     * @throws \ErrorException
     */
    static function &get(string $a_path)
    {
        if (file_exists($a_path)) {
            $real_path = realpath($a_path);
            
            if (!isset(self::getInstance()->jsons[$real_path])) {
                $json = json_decode(file_get_contents($real_path));
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \ErrorException('Invalid JSON');
                }
                
                self::getInstance()->jsons[$real_path] = $json;
            }
            
            return self::getInstance()->jsons[$real_path];
        } else {
            throw new \ErrorException('File not found');
        }
    }
    
    /**
     * JsonStore destructor
     *
     * Save the content of opened files at the end of the execution
     */
    function __destruct()
    {
        foreach (self::getInstance()->jsons as $file_path => $content) {
            $string = json_encode($content, JSON_PRETTY_PRINT);
    
            try {
                if (false !== $string) {
                    $resource = fopen($file_path, 'c+');
                    fwrite($resource, $string);
                    fclose($resource);
                } else {
                    throw new \RuntimeException(sprintf("Cannot convert content of file %s to valid JSON", $file_path));
                }
            } catch (\RuntimeException $e) {
                throw $e;
            } catch (\Throwable $e) {
                throw new \RuntimeException(sprintf("An error occured while attempting to write to %s", $file_path));
            }
        }
    }
}