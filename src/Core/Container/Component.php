<?php
/**
 * Component
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


namespace Apine\Core\Container;


/**
 * Class Component
 *
 * @package Apine\Core\Container
 */
class Component
{
    private $id;
    
    /**
     * @var mixed
     */
    private $content;
    
    /**
     * @var bool
     */
    private $factory = false;
    
    /**
     * @var mixed
     */
    private $computed;
    
    /**
     * Component constructor.
     *
     * @param string $id
     * @param mixed|callable|object $content
     * @param bool $factory
     */
    public function __construct(string $id, $content, bool $factory = false)
    {
        $this->id = $id;
        $this->content = $content;
        $this->factory = $factory;
    }
    
    public function isFactory() : bool
    {
        return $this->factory;
    }
    
    public function getId() : string
    {
        return $this->id;
    }
    
    public function hasType(string $type) : bool
    {
        $value = $this->invoke();
        
        if (is_scalar($value) || is_array($value) || is_resource($value) || is_null($value)) {
            return gettype($value) === $type;
        } else {
            return $value instanceof $type;
        }
    }
    
    /**
     * @return mixed
     */
    public function invoke()
    {
        if (!$this->factory) {
            if (null === $this->computed) {
                if (is_callable($this->content)) {
                    $this->computed = ($this->content)();
                } else {
                    $this->computed = $this->content;
                }
            }
            
            return $this->computed;
        } else {
            return ($this->content)();
        }
    }
}