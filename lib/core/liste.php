<?php
/**
 * Liste Collection
 * This file contains the Liste collection Class
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Liste Collection
 * Traversable collection that mimics an array while providing easy to use features
 */
class Liste implements IteratorAggregate {

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
		} catch (Exception $e) {
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
	 * @throws Exception If cannot fetch the item fromthe collection
	 * @return mixed |boolean
	 */
	public function get_item ($a_key) {

		try {
			
			if ($this->length() == 0) {
				throw new Exception();
			}
			
			if ($this->key_exists($a_key)) {
				return $this->items[$a_key];
			} else {
				throw new Exception();
			}
			
		} catch(Exception $e) {
			return false;
		}
	
	}

	/**
	 * Fetch all items from the collection
	 * 
	 * @throws Exception If cannot fetch items from the collection
	 * @return multitype:mixed |boolean
	 */
	public function get_all () {

		try {
			
			if ($this->length() == 0) {
				throw new Exception();
			}
			
			return $this->items;
		} catch (Exception $e) {
			return false;
		}
	
	}

	/**
	 * Retrieve the first item from the collection
	 * 
	 * @throws Exception If cannot fetch the item from the collection
	 * @return mixed|boolean
	 */
	public function get_first () {

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
	 * @throws Exception If cannot fetch the item from the collection
	 * @return mixed|boolean
	 */
	public function get_last () {

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
	 * Sort item from the collection in reverse order
	 * 
	 * @throws Exception If cannot reverse the collection
	 * @return boolean
	 */
	public function reverse () {

		try {
			
			if ($this->length() == 0) {
				throw new Exception();
			}
			
			$this->items = array_reverse($this->items);
			return true;
		} catch (Exception $e) {
			return false;
		}
	
	}

	/**
	 * Sort items from collection by key
	 * 
	 * @throws Exception If cannot sort the collection
	 * @return boolean
	 */
	public function ksort () {

		try {
			
			if ($this->length() == 0) {
				throw new Exception();
			}
			
			ksort($this->items);
			return true;
		} catch (Exception $e) {
			return false;
		}
	
	}

	/**
	 * Get an array of every item keys in the collection
	 * 
	 * @throws Exception If cannot fetch item keys
	 * @return string[]|boolean
	 */
	public function keys () {

		try {
			
			if($this->items != null) {
				
				if ($this->length() == 0) {
					throw new Exception();
				}
				
				return array_keys($this->items);
			} else {
				return array();
			}
			
		} catch (Exception $e) {
			return false;
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
	 * @throws Exception If cannot verify the key with the collection
	 * @return boolean
	 */
	public function key_exists ($a_key) {

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
	 * 
	 * @param mixed $a_value
	 *        Value to verify
	 * @throws Exception If the collection is empty
	 * @return boolean
	 */
	public function value_exists ($a_value) {
		
		try {
			if ($this->length() == 0) {
				throw new Exception();
			}
			
			$match_index = null;
			$match_item = false;
			
			// Cycle through every items in the Liste
			foreach ($this->items as $key => $item) {
				// The value tested must be of a compatible type
				// against the matching value in the Liste
				
				// Scalar types (string, integer, float and boolean) can be
				// converted easily from one to another thus can be tested together
				if ((is_string($a_value) || is_numeric($a_value)) &&
					(is_string($item) || is_numeric($item))) {
						
					if ($a_value == $item) {
						$match_index = $key;
						$match_item = true;
					}
					
				// If fact boolean are kind of fucked up and
				// need a seperate condition
				} else if (is_bool($a_value) && is_bool($item)) {
					
					if ($a_value === $item) {
						$match_index = $key;
						$match_item = true;
					}
					
				// Arrays are kind of special and therefore need
				// a different test method
				} else if (is_array($a_value) && is_array($item)) {
					$diff = array_diff($a_value,$item);
					
					if (count($diff) == 0) {
						$match_index = $key;
						$match_item = true;
					}
					
				// Objects are aggregations of the two previous
				} else if ((is_object($a_value) && is_object($item)) && (get_class($a_value) && get_class($item))) {
					
					// If objects are both ApineEntity we can easily
					// check for their ids
					if (get_parent_class($a_value) == "ApineEntityModel" &&
						get_parent_class($item) == "ApineEntityModel") {
						
						if ($a_value->get_id() == $item->get_id()) {
							$match_index = $key;
							$match_item = true;
						}
						
					} else {
						$array_value = (array) $a_value;
						$array_item = (array) $item;
						
						if (get_class($a_value) == get_class($item)) {
							$diff = array_diff_assoc($array_value,$array_item);
							
							if (count($diff) == 0) {
								$match_index = $key;
								$match_item = true;
							}
							
						}
						
					}
					
				}
				
				// If a match is found get out of the loop 
				if ($match_item===true) {
					break;
				}
				
			}
			
			return $match_item;
		} catch(Exception $e) {
			return false;
		}
		
	}

	/**
	 * Get Liste's iterator (non-PHPdoc)
	 * 
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {

		return new ListeIterator(clone $this);
	
	}

}