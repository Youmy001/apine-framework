<?php
/**
 * Liste Collection
 * This file contains the Liste collection Class
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

/**
 * Liste Collection
 * 
 * Traversable collection that mimics an array while providing easy to use features
 */
final class Collection implements \IteratorAggregate {

	/**
	 * Object array
	 * 
	 * @var mixed[]
	 */
	private $items;

	/**
	 * Add an item to the collection
	 * 
	 * @param mixed $a_item
	 *        Item to add to the collection
	 * @param string $a_key
	 *        Predifined key of the item into the collection. It is
	 *        possible to override existing values, so it is
	 *        recommended to not specify a key at the insertion of a
	 *        new item.
	 * @throws Exception If cannot insert item into the collection
	 * @return mixed|boolean
	 */
	public function add_item ($a_item, $a_key = null) {

		try {
			
			// Add the item to the collection
			if (is_null($a_key)) {
				$this->items[] = $a_item;
			} else {
				$this->items[$a_key] = $a_item;
			}
			
			// Retrieve and return the key
			return array_search($a_item, $this->items, true);
		} catch (\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Remove an item from the collection
	 * 
	 * @param string $a_key
	 *        Key of the item to remove
	 * @throws Exception If cannot remove item from the collection
	 * @return boolean
	 */
	public function remove_item ($a_key) {

		try {
			
			if ($this->length() > 0) {
				
				if ($this->key_exists($a_key)) {
					unset($this->items[$a_key]);
				} else {
					throw new \Exception();
				}
				
			} else {
				throw new \Exception();
			}
			
			return true;
		} catch (\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Fetch an item from the collection
	 * 
	 * @param string $a_key
	 *        Key of the item to fetch
	 * @throws Exception If cannot fetch the item fromthe collection
	 * @return mixed |boolean
	 */
	public function get_item ($a_key) {

		try {
			
			if ($this->length() == 0) {
				throw new \Exception();
			}
			
			if ($this->key_exists($a_key)) {
				return $this->items[$a_key];
			} else {
				throw new \Exception();
			}
			
		} catch(\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Fetch all items from the collection
	 * 
	 * @return mixed
	 */
	public function get_all () {

		try {
			
			if ($this->length() == 0) {
				throw new \Exception();
			}
			
			return $this->items;
		} catch (\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Retrieve the first item from the collection
	 * 
	 * @return mixed
	 */
	public function get_first () {

		try {
			
			if ($this->length() == 0) {
				throw new \Exception();
			}
			
			return reset($this->items);
		} catch (\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Retrieve the last item from the collection
	 * 
	 * @return mixed
	 */
	public function get_last () {

		try {
			
			if ($this->length() == 0) {
				throw new \Exception();
			}
			
			return end($this->items);
		} catch (\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Sort item from the collection in reverse order
	 * 
	 * @return boolean
	 */
	public function reverse () {

		try {
			$this->items = array_reverse($this->items);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	
	}

	/**
	 * Sort items from collection by key
	 * 
	 * @return boolean
	 */
	public function ksort () {

		return ksort($this->items);
	
	}

	/**
	 * Get an array of every item keys in the collection
	 * 
	 * @return string[]
	 */
	public function keys () {

		if($this->items != null && $this->length() != 0) {
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
	public function length () {

		return sizeof($this->items);
	
	} 

	/**
	 * Verify if key exists in the collection
	 * 
	 * @param string $a_key
	 *        Key to verify
	 * @return boolean
	 */
	public function key_exists ($a_key) {

		try {
			
			if ($this->length() == 0) {
				throw new \Exception();
			}
			
			return (isset($this->items[$a_key]));
		} catch (\Exception $e) {
			return false;
		}
	
	}
	
	/**
	 * Verify if value exists in the collection
	 * 
	 * @param mixed $a_value
	 *        Value to verify
	 * @return boolean
	 */
	public function value_exists ($a_value) {
		
		try {
			if ($this->length() == 0) {
				return false;
			}
			
			if (is_object($a_value) && get_parent_class($a_value) === '\Apine\Entity\EntityModel') {
				$a_value->load();
			}
			
			$serial_value = serialize($a_value);
			
			// Cycle through every items in the Liste
			foreach ($this->items as $key => $item) {
				if (is_object($item) && get_parent_class($item) === '\Apine\Entity\EntityModel') {
					$item->load();
				}
				
				if ($serial_value === serialize($item)) {
					return true;
				}
			}
			
			return false;
		} catch(\Exception $e) {
			return false;
		}
		
	}

	/**
	 * Get Liste's iterator (non-PHPdoc)
	 * 
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {

		return new CollectionIterator(clone $this);
	
	}

}
