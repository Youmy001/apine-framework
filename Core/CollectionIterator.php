<?php
/**
 * Liste Collection Iterator
 * This file contains an iterator for the Liste Collection
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

final class CollectionIterator implements \Iterator {

	/**
	 * Instance of the collection
	 * 
	 * @var Liste
	 */
	private $_collection;

	/**
	 * Current pointer position in the collection
	 * 
	 * @var integer
	 */
	private $_currIndex = 0;

	/**
	 * Array of all keys into the collection
	 * 
	 * @var string[]
	 */
	private $_keys;

	/**
	 * Iterator's constructor
	 * 
	 * @param Liste $a_liste
	 *        Instance of the collection
	 */
	public function __construct (Collection $a_collection) {

		$this->_collection = $a_collection;
		$this->_keys = $this->_collection->keys();
	
	}

	/**
	 * Return to the first item (non-PHPdoc)
	 * 
	 * @see Iterator::rewind()
	 */
	public function rewind () {

		$this->_currIndex = 0;
	
	}

	/**
	 * Return current item's key (non-PHPdoc)
	 * 
	 * @see Iterator::key()
	 */
	public function key () {

		return $this->_keys[$this->_currIndex];
	
	}

	/**
	 * Get the item at current pointer position (non-PHPdoc)
	 * 
	 * @see Iterator::current()
	 */
	public function current () {

		return $this->_collection->get_item($this->_keys[$this->_currIndex]);
	
	}

	/**
	 * Move the pointer to the next position (non-PHPdoc)
	 * 
	 * @see Iterator::next()
	 */
	public function next () {

		$this->_currIndex++;
	
	}

	/**
	 * Validate pointer's position (non-PHPdoc)
	 * 
	 * @see Iterator::valid()
	 */
	public function valid () {

		return $this->_currIndex < $this->_collection->length();
	
	}

}
