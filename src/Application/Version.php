<?php
/**
 * Versioning validator
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */

namespace Apine\Application;

use Apine\Application\Application;
use Apine\Core\Config;
use RuntimeException;

/**
 * Verifies version numbers for modules
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Application
 */
final class Version
{
    /**
     * Regular expression for a valid semantic version number
     *
     * @var string
     */
    const VERSION_REGEX = '/^(0|[1-9]\d*)(\.(0|[1-9]\d*)){1,2}(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/';
    
    private $framework;
    
    private $application;
    
    public function __construct()
    {
        try {
            if (!self::validate(Application::$version)) {
                throw new RuntimeException('Invalid Framework Version Number');
            } else {
                $this->framework = Application::$version;
            }
            
            $config = new Config('config/application');
            
            if (!self::validate($config->version)) {
                throw new RuntimeException('Invalid Application Version Number');
            }
    
            $this->application = $config->version;
        } catch (\Exception $e) {
            return;
        }
    }
    
    /**
     * Check application version number
     *
     * @return string
     */
    public function application()
    {
        return $this->application;
    }
    
    /**
     * Check framework version number
     *
     * @return string
     */
    public function framework()
    {
        return $this->framework;
    }
    
    /**
     * Validate version number
     *
     * @param string $version
     *
     * @return boolean
     */
    public static function validate($version)
    {
        return (true == preg_match(self::VERSION_REGEX, $version));
    }
}
