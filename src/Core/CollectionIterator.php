<?php
/**
 * Liste Collection Iterator
 * This file contains an iterator for the Liste Collection
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Core;

/**
 * Class CollectionIterator
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class CollectionIterator implements \Iterator
{
    /**
     * Instance of the collection
     *
     * @var Collection
     */
    private $collection;
    
    /**
     * Current pointer position in the collection
     *
     * @var integer
     */
    private $currIndex = 0;
    
    /**
     * Array of all keys into the collection
     *
     * @var string[]
     */
    private $keys;
    
    /**
     * Iterator's constructor
     *
     * @param Collection $a_collection
     *        Instance of the collection
     */
    public function __construct(Collection $a_collection)
    {
        $this->collection = $a_collection;
        $this->keys = $this->collection->keys();
    }
    
    /**
     * Return to the first item (non-PHPdoc)
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->currIndex = 0;
    }
    
    /**
     * Return current item's key (non-PHPdoc)
     *
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->keys[$this->currIndex];
    }
    
    /**
     * Get the item at current pointer position (non-PHPdoc)
     *
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->collection->getItem($this->keys[$this->currIndex]);
    }
    
    /**
     * Move the pointer to the next position (non-PHPdoc)
     *
     * @see Iterator::next()
     */
    public function next()
    {
        $this->currIndex++;
    }
    
    /**
     * Validate pointer's position (non-PHPdoc)
     *
     * @see Iterator::valid()
     */
    public function valid()
    {
        return $this->currIndex < $this->collection->length();
    }
}
