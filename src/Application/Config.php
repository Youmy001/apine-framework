<?php
/**
 * Configuration Reader
 * This script contains an helper to read configuration files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Application;

/**
 * Configuration Reader
 * Read and write project's configuration file
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Application
 * @deprecated
 */
final class Config
{
    /**
     * Instance of the Config reader
     * Singleton Implementation
     *
     * @var Config
     */
    private static $instance;
    
    
    /**
     * Setting strings extracted from the configuration file
     *
     * @var array
     */
    private $settings;
    //private $settings = [];
    
    /**
     * Construct the Conguration Reader handler
     * Extract string from the configuration file
     */
    private function __construct()
    {
        $this->settings = Application::getInstance()->getConfig()->getConfig();
    }
    
    /**
     * Singleton design pattern implementation
     *
     * @static
     * @return Config
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     * Fetch a configuration string
     *
     * @param string $prefix
     * @param string $key
     *
     * @return string
     */
    public static function get($prefix, $key)
    {
        $prefix = strtolower($prefix);
        $key = strtolower($key);
        
        return isset(self::getInstance()->settings[$prefix][$key]) ? self::getInstance()->settings[$prefix][$key] : null;
    }
    
    /**
     * Fetch all configuration strings
     *
     * @return array
     */
    public static function getConfig()
    {
        return self::getInstance()->settings;
    }
    
    /**
     * Write or update a configuration string
     *
     * @param string $prefix
     * @param string $key
     * @param string $value
     */
    public static function set($prefix, $key, $value)
    {
        $prefix = strtolower($prefix);
        $key = strtolower($key);
        
        self::getInstance()->settings[$prefix][$key] = $value;
        
        // Update the parent and the file
        Application::getInstance()->getConfig()->set($prefix, $key, $value);
    }
}