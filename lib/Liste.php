<?php
/**
 * This file contains the List collection Class
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */
require_once ('lib/Liste_Iterator.php');

/**
 * Traversable collection that mimics an array while providing easy to
 * use features
 */
class Liste implements IteratorAggregate{

	/**
	 * Object array
	 * @var mixed[]
	 */
	private $items;

	/**
	 * Add an item to the collection
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
	public function add_item($a_item, $a_key = null){

		try{
			// Add the item to the collection
			if(is_null($a_key)){
				$this->items[] = $a_item;
			}else{
				$this->items[$a_key] = $a_item;
			}
			// Retrieve and return the key
			return array_search($a_item, $this->items, true);
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Remove an item from the collection
	 * @param string $a_key
	 *        Key of the item to remove
	 * @throws Exception If cannot remove item from the collection
	 * @return boolean
	 */
	public function remove_item($a_key){

		try{
			if($this->length() > 0){
				if($this->exists($a_key))
					unset($this->items[$a_key]);
				else
					throw new Exception('The provided key does not exist.');
			}else{
				throw new Exception('The collection is empty.');
			}
			return true;
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Fetch an item from the collection
	 * @param string $a_key
	 *        Key of the item to fetch
	 * @throws Exception If cannot fetch the item fromthe collection
	 * @return mixed |boolean
	 */
	public function get_item($a_key){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			if($this->exists($a_key)){
				return $this->items[$a_key];
			}else{
				throw new Exception('The provided key does not exist.');
			}
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Fetch all items from the collection
	 * @throws Exception If cannot fetch items from the collection
	 * @return multitype:mixed |boolean
	 */
	public function get_all(){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			return $this->items;
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Retrieve the first item from the collection
	 * @throws Exception If cannot fetch the item from the collection
	 * @return mixed|boolean
	 */
	public function get_first(){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			return reset($this->items);
		}catch(Exception $e){
			throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Retrieve the last item from the collection
	 * @throws Exception If cannot fetch the item from the collection
	 * @return mixed|boolean
	 */
	public function get_last(){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			return end($this->items);
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Sort item from the collection in reverse order
	 * @throws Exception If cannot reverse the collection
	 * @return boolean
	 */
	public function reverse(){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			$this->items = array_reverse($this->items);
			return true;
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Sort items from collection by key
	 * @throws Exception If cannot sort the collection
	 * @return boolean
	 */
	public function ksort(){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			ksort($this->items);
			return true;
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Get an array of every item keys in the collection
	 * @throws Exception If cannot fetch item keys
	 * @return string[]|boolean
	 */
	public function keys(){

		try{
			if($this->items != null){
				if($this->length() == 0)
					throw new Exception('The collection is empty.');
				return array_keys($this->items);
			}else{
				return array();
			}
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Count all items in the collection
	 * @return integer
	 */
	public function length(){

		return sizeof($this->items);
	
	}

	/**
	 * Verify if key exists in the collection
	 * @param string $a_key
	 *        Key to verify
	 * @throws Exception If cannot verify the key with the collection
	 * @return boolean
	 */
	public function exists($a_key){

		try{
			if($this->length() == 0)
				throw new Exception('The collection is empty.');
			return (isset($this->items[$a_key]));
		}catch(Exception $e){
			//throw new Exception($e->getMessage() . ' - ' . $e->getTraceAsString());
			return false;
		}
	
	}

	/**
	 * Get Liste's iterator (non-PHPdoc)
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator(){

		return new Liste_Iterator(clone $this);
	
	}
	
	/**
	 * @deprecated
	 * @return string
	 */
	public function __toString(){

		$string = "";
		for($i = 0;$i < $this->length();$i++){
			if($i > 0)
				$string .= ', ';
			$string .= $this->get_item($i)->getName();
		}
		return $string;
	
	}

}