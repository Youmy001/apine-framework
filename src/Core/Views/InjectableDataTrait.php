<?php
/**
 * InjectableDataTrait
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Views;

/**
 * Trait InjectableDataTrait
 *
 * @package Apine\Core\Views
 */
trait InjectableDataTrait
{
    protected $attributes = [];
    
    /**
     * Add an attribute to the view.
     * Overwrites previous value of the same name.
     * An attribute is data to be injected into the body of
     * the response is the type of View supports it
     *
     * @param string $name
     * @param null   $value
     */
    function addAttribute(string $name, $value = null)
    {
        $this->attributes[$name] = $value;
    }
    
    /**
     * Remove an attribute from the view
     *
     * @param string $name
     */
    function removeAttribute(string $name)
    {
        unset($this->attributes[$name]);
    }
    
    function getAttributes() : array
    {
        return $this->attributes;
    }
    
    function getAttribute(string $name, $default = null)
    {
        if (false === array_key_exists($name, $this->attributes)) {
            return $default;
        }
    
        return $this->attributes[$name];
    }
    
    /**
     * Replace the attributes by new one
     * This method completely overwrites
     * the attributes
     *
     * @param array $data
     */
    function setAttributes(array $data = [])
    {
        $this->attributes = $data;
    }
}