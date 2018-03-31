<?php
/**
 * Collection
 * This file contains the collection Class
 *
 * @license MIT
 * @copyright 2015-18 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core;

/**
 * Collection
 * Traversable collection that mimics an array while providing easy to use features
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Object array
     *
     * @var mixed[]
     */
    private $items;
    
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
    /**
     * Add an item to the collection
     *
     * @param mixed      $item
     *        Item to add to the collection
     * @param string|int $key
     *        Predefined key of the item into the collection. It is
     *        possible to override existing values, so it is
     *        recommended to not specify a key at the insertion of a
     *        new item.
     *
     * @return string|int
     */
    public function set($item, $key = null)
    {
        // Add the item to the collection
        if (is_null($key)) {
            $this->items[] = $item;
        } else {
            $this->items[$key] = $item;
        }
        
        // Retrieve and return the key
        return array_search($item, $this->items, true);
    }
    
    /**
     * Remove an item from the collection
     *
     * @param string|int $key
     *        Key of the item to remove
     *
     * @throws \RuntimeException If index not found
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->items[$key]);
        } else {
            throw new \RuntimeException(sprintf("Index %s was not found in the collection", $key));
        }
    }
    
    /**
     * Fetch an item from the collection
     *
     * @param string|int $key
     *        Key of the item to fetch
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }
        
        return null;
    }
    
    /**
     * Fetch all items from the collection
     *
     * @return mixed
     */
    public function all()
    {
        return $this->items;
    }
    
    /**
     * Retrieve the first item from the collection
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }
    
    /**
     * Retrieve the last item from the collection
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }
    
    /**
     * Add multiple items at once.
     *
     * @param array $items
     */
    public function inject(array $items): void
    {
        //$this->items = array_merge($this->items, $items);
        foreach ($items as $key => $value) {
            $this->set($value, $key);
        }
    }
    
    /**
     * Sort item from the collection in reverse order
     */
    public function reverse()
    {
        $this->items = array_reverse($this->items);
    }
    
    /**
     * Get an array of every item keys in the collection
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }
    
    /**
     * Count all items in the collection
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
    
    /**
     * Verify if key exists in the collection
     *
     * @param string $a_key
     *        Key to verify
     *
     * @return boolean
     */
    public function has($a_key): bool
    {
        return (isset($this->items[$a_key]));
    }
    
    public function clear()
    {
        $this->items = [];
    }
    
    /**
     * Get collection iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
    
    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string|int $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }
    
    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string|int $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param string|int    $offset The offset to assign the value to.
     * @param mixed         $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($value, $offset);
    }
    
    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string|int $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
