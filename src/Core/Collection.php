<?php
/**
 * Liste Collection
 * This file contains the Liste collection Class
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Core;

use Apine\Exception\GenericException;
use Exception;

/**
 * Collection
 * Traversable collection that mimics an array while providing easy to use features
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 * @deprecated
 */
final class Collection implements \IteratorAggregate
{
    /**
     * Object array
     *
     * @var mixed[]
     */
    private $items;
    
    /**
     * Add an item to the collection
     *
     * @param mixed  $a_item
     *        Item to add to the collection
     * @param string $a_key
     *        Predifined key of the item into the collection. It is
     *        possible to override existing values, so it is
     *        recommended to not specify a key at the insertion of a
     *        new item.
     *
     * @throws Exception If cannot insert item into the collection
     * @return mixed|boolean
     */
    public function addItem($a_item, $a_key = null)
    {
        try {
            
            // Add the item to the collection
            if (is_null($a_key)) {
                $this->items[] = $a_item;
            } else {
                $this->items[$a_key] = $a_item;
            }
            
            // Retrieve and return the key
            return array_search($a_item, $this->items, true);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Remove an item from the collection
     *
     * @param string $a_key
     *        Key of the item to remove
     *
     * @throws Exception If cannot remove item from the collection
     * @return boolean
     */
    public function removeItem($a_key)
    {
        try {
            
            if ($this->length() > 0) {
                
                if ($this->key_exists($a_key)) {
                    unset($this->items[$a_key]);
                } else {
                    throw new Exception();
                }
                
            } else {
                throw new Exception();
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Fetch an item from the collection
     *
     * @param string $a_key
     *        Key of the item to fetch
     *
     * @throws Exception If cannot fetch the item fromthe collection
     * @return mixed |boolean
     */
    public function getItem($a_key)
    {
        try {
            
            if ($this->length() == 0) {
                throw new Exception();
            }
            
            if ($this->key_exists($a_key)) {
                return $this->items[$a_key];
            } else {
                throw new Exception();
            }
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Fetch all items from the collection
     *
     * @return mixed
     */
    public function getAll()
    {
        try {
            
            if ($this->length() == 0) {
                throw new Exception();
            }
            
            return $this->items;
        } catch (Exception $e) {
            return array();
        }
    }
    
    /**
     * Retrieve the first item from the collection
     *
     * @return mixed
     */
    public function getFirst()
    {
        try {
            
            if ($this->length() == 0) {
                throw new Exception();
            }
            
            return reset($this->items);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Retrieve the last item from the collection
     *
     * @return mixed
     */
    public function getLast()
    {
        try {
            
            if ($this->length() == 0) {
                throw new Exception();
            }
            
            return end($this->items);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Add multiple items at once.
     *
     * @param array $a_items
     */
    public function injectItems(array $a_items)
    {
        if (!empty($a_items)) {
            $this->items = array_merge($this->items, $a_items);
        }
    }
    
    /**
     * Sort item from the collection in reverse order
     *
     * @return boolean
     */
    public function reverse()
    {
        try {
            $this->items = array_reverse($this->items);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Sort items from collection by key
     *
     * @return boolean
     */
    public function ksort()
    {
        return ksort($this->items);
    }
    
    /**
     * Get an array of every item keys in the collection
     *
     * @return string[]
     */
    public function keys()
    {
        if ($this->items != null && $this->length() != 0) {
            return array_keys($this->items);
        } else {
            return array();
        }
    }
    
    /**
     * Count all items in the collection
     *
     * @return integer
     */
    public function length()
    {
        return sizeof($this->items);
    }
    
    /**
     * Verify if key exists in the collection
     *
     * @param string $a_key
     *        Key to verify
     *
     * @return boolean
     */
    public function keyExists($a_key)
    {
        try {
            
            if ($this->length() == 0) {
                throw new Exception();
            }
            
            return (isset($this->items[$a_key]));
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Verify if value exists in the collection
     * Although it is possible to use this method with
     * EntityModel, the serial validation may give
     * unreliable results depending on your implementation
     * of the entity, thus we do not recommend it.
     *
     * @param mixed $a_value
     *        Value to verify
     *
     * @return boolean
     */
    public function valueExists($a_value)
    {
        try {
            if ($this->length() == 0) {
                return false;
            }
            
            if (is_object($a_value) && (is_a($a_value, '\Apine\Entity\EntityModel') || is_a($a_value,
                        '\Apine\Entity\Overload\EntityModel'))
            ) {
                if (is_a($a_value, '\Apine\Entity\Overload\EntityModel')) {
                    $a_value->get_id();
                } else {
                    $a_value->load();
                }
            }
            
            $serial_value = serialize($a_value);
            
            // Cycle through every items in the collection
            foreach ($this->items as $key => $item) {
                if (is_object($item) && (is_a($item, '\Apine\Entity\EntityModel') || is_a($item,
                            '\Apine\Entity\Overload\EntityModel'))
                ) {
                    if (is_a($item, '\Apine\Entity\Overload\EntityModel')) {
                        $item->get_id();
                    } else {
                        $item->load();
                    }
                }
                
                if ($serial_value === serialize($item)) {
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get Collection iterator (non-PHPdoc)
     *
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new CollectionIterator(clone $this);
    }
}
