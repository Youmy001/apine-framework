<?php
namespace Apine\Modules\Core\MVC;

class ApineTwigHelper {
    private $globals = array();
    private static $instance;

    /**
     * Protected constructor to prevent creating a new instance of the class.
     */
    protected function __construct() {

    }

    /**
     * Private clone method to prevent cloning of the instance.
     * @return void
     */
    protected function __clone() {

    }

    /**
     * Private unserialize method to prevent unserializing.
     * @return void
     */
    protected function __wakeup() {

    }

    /**
     * Returns the instance of this class.
     * @return static
     */
    public static function instance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Adds or update a global variable to the array of globals.
     * @param $name string The name of the global variable.
     * @param $value string The value of the variable.
     * @return void
     */
    public static function add_global($name, $value) {
        self::instance()->globals[$name] = $value;
    }

    /**
     * Get all global variables.
     * @return array
     */
    public static function get_global() {
        return self::instance()->globals;
    }
}