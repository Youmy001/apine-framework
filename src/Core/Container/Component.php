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
    /**
     * @var string
     */
    private $name;
    
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
     * @param string $name
     * @param mixed|callable|object $content
     * @param bool $factory
     */
    public function __construct(string $name, $content, bool $factory = false)
    {
        if (true === $factory && (!is_callable($content) || !($content instanceof \Closure))) {
            throw new \RuntimeException('A factory must be a callable');
        }
        
        $this->name = $name;
        $this->content = $content;
        $this->factory = $factory;
    }
    
    /**
     * @return bool
     */
    public function isFactory() : bool
    {
        return $this->factory;
    }
    
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * @param string $type
     *
     * @return bool
     * @throws \Throwable
     */
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
     * @throws \Throwable   If an error occurs while reading
     *                      the content of the component
     */
    public function invoke()
    {
        try {
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
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}