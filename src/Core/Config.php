<?php
/**
 * Configuration Reader
 * This script contains an helper to read configuration files
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core;

use Apine\Core\Json\Json;

/**
 * Configuration Reader
 * Read and write project's configuration file
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Config
{
    /**
     * Path to the config file
     *
     * @var string
     */
    private $path;
    
    /**
     * Setting strings for the file
     *
     * @var Json
     */
    private $settings;
    
    /**
     * Construct the Configuration Reader handler
     * Extract string from the configuration file
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    
        if (file_exists($path)) {
            $this->settings = new Json(file_get_contents($path));
        } else {
            $this->settings = new Json(null);
        }
    }
    
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->settings->$name;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) : void
    {
        $this->settings->$name = $value;
    }
    
    public function __isset(string $name) : bool
    {
        return isset($this->settings->$name);
    }
    
    public function getPath () : string
    {
        return $this->path;
    }
    
    public function save()
    {
        $resource = fopen($this->path, 'w+');
        fwrite($resource, (string) $this->settings);
        fclose($resource);
    }
}