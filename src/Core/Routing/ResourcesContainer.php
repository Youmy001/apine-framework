<?php
/**
 * ResourcesContainer
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

namespace Apine\Core\Routing;

use Apine\Core\Container\Container;


/**
 * Class ResourcesContainer
 *
 * @package Apine\Core\Routing
 */
class ResourcesContainer extends Container
{
    public function toArray() : array
    {
        $array = array();
        
        foreach ($this->entries as $name => $value) {
            $array[$name] = $this->get($name);
        }
        
        return $array;
    }
}