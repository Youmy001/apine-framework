<?php
/**
 * Json
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Json;

/**
 * Class Json
 *
 * @package Apine\Core\Json
 */
class Json
{
    /**
     * @var \StdClass
     */
    private $data;
    
    /**
     * Json constructor.
     *
     * @param string|array|\StdClass $data
     *
     * @throws JsonInvalidFormatException
     */
    public function __construct($data)
    {
        if (is_string($data) && !empty($data)) {
            $this->data = json_decode($data);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JsonInvalidFormatException('Invalid JSON string');
            }
        } else if (is_array($data) || $data instanceof \StdClass) {
            $this->data = (object) $data;
        } else {
            $this->data = new \StdClass();
        }
    }
    
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data->$name;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) : void
    {
        $this->data->$name = $value;
    }
    
    public function __isset(string $name) : bool
    {
        return isset($this->data->$name);
    }
    
    public function __unset(string $name) : void
    {
        unset($this->data->$name);
    }
    
    public function __toString() : string
    {
        $string = json_encode($this->data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    
        return $string;
    }
}