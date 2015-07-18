<?php

/**
 * Ressource files gestion
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */
class File{

	/**
	 * Ressource file
	 * @var resource
	 */
	protected $file;

	/**
	 * File path
	 * @var string
	 */
	private $file_location;

	/**
	 * File name
	 * @var string
	 */
	private $file_name;

	/**
	 * Loading error
	 * @var integer
	 */
	private $file_error;

	/**
	 * Saving location
	 * @var string
	 */
	private $save_loc;

	/**
	 * Has a saving location been defined
	 * @var boolean
	 */
	private $_is_save_loc = false;

	/**
	 * Is a new file
	 * @var boolean
	 */
	protected $is_new;

	/**
	 * File class' constructor
	 * @param string $location        
	 */
	public function __construct($location = null){

		if($location != null){
			try{
				// Open File
				$this->file = fopen($location, "c+");
				$this->file_location = $location;
				$this->file_name = basename($location);
				// Get file extention
				$ar_file_ext = explode(".", $this->file_name);
				$extension = end($ar_file_ext);
				// Get file name with extention
				$ar_file_name = explode("/", reset($ar_file_ext));
				$file_name = end($ar_file_name);
				$this->file_name = $file_name . "." . $extension;
				$this->save_loc = substr($this->file_location, 0, strripos($location, "/") - 1);
				$this->file_error = 0;
			}catch(Exception $e){
				$this->file_error = 4;
			}
		}
	
	}

	/**
	 * Open a new file from an upload
	 * @param array $file_array
	 *        Array containning temporary file's informations. It's
	 *        basicaly the $_FILE['name_of_a_input_file'] array.
	 */
	public function new_file(Array $file_array){

		if($file_array['tmp_name']){
			$this->file = fopen($file_array['tmp_name'], "c+");
			$this->file_location = $file_array['tmp_name'];
			$this->file_name = $file_array['name'];
			$this->file_error = $file_array['error'];
			$this->file_array = $file_array;
			$this->is_new = true;
		}
	
	}

	/**
	 * Fetch file size
	 * @return integer
	 */
	public function get_size(){

		if($this->is_new)
			$size = $this->file_array['size'];
		else
			$size = filesize($this->get_location());
		return $size;
	
	}

	/**
	 * Fetch file name
	 * @return string
	 */
	public function get_file_name(){

		return $this->file_name;
	
	}

	/**
	 * Set file name
	 * @param string $name
	 *        New File name
	 */
	public function set_file_name($name){

		$ar_file_ext = explode(".", $this->file_name);
		$extension = end($ar_file_ext);
		// Add extention if its not present into the new name
		if(strripos($name, '.') === false){
			$this->file_name = $name . "." . $extension;
		}else{
			$this->file_name = $name;
		}
	
	}

	/**
	 * Fetch file path
	 * @return string
	 */
	public function get_location(){

		return $this->file_location;
	
	}

	/**
	 * Alias for File::get_location()
	 * @see File::get_location()
	 * @return string
	 */
	public function get_path(){

		return $this->get_location();
	
	}

	/**
	 * Fetch the saving path
	 * @return string|boolean
	 */
	public function get_save_location(){

		if($this->_is_save_loc)
			return $this->save_loc;
		else
			return false;
	
	}

	/**
	 * Set saving path
	 * @param string $location
	 *        Saving location
	 */
	public function set_save_location($location){

		$this->_is_save_loc = true;
		$this->save_loc = $location;
	
	}

	/**
	 * Fetch error code for new upload files only.
	 * @return integer
	 */
	public function get_error_code(){
		// For upload only
		if($this->is_new){
			return $this->file_error;
		}else{
			return 0;
		}
	
	}

	/**
	 * Get file's mime type
	 * @return string
	 */
	public function get_type(){

		if($this->is_new)
			$type = $this->file_array["type"];
		else
			$type = mime_content_type($this->file_location);
		return $type;
	
	}

	/**
	 * Get file's extention
	 * @return string
	 */
	public function get_extention(){

		$filename = explode(".", $this->get_file_name());
		return end($filename);
	
	}

	/**
	 * Remove file from disk.
	 * This action literaly erases the ressource from the hard drive.
	 */
	public function delete(){

		unlink($this->get_location());
	
	}

	/**
	 * Save the file to its saving location.
	 * If no save locations are specified, it will save the file to
	 * its current location. Returns <b><i>true</i></b> on success.
	 * @param boolean $move Should the file be moved or copied
	 * @return boolean
	 */
	public function save($move=false){

		if($this->_is_save_loc){
			$filename=basename($this->file_name);
			$target=$this->save_loc;
			$target=$target.$filename;
			$file = $this->file_location;
			$name = substr($file, strrpos($file, '/')+1);
		
			
			$target = $this->save_loc;
			$target = $target . $filename;
			
			if($move==false || $this->is_new){
				$success = copy($this->file_location, $target);
			}else{
				$success = rename($this->file_location, $target, $this->file);
			}
			$this->file_location = $target;
			return $success;
		}else{
			return false;
		}
	
	}
	
	/**
	 * Move the file to its saving location.
	 * @return boolean
	 */
	public function move(){
		return $this->save(true);
	}
	
	public function read(){
	
		readfile($this->get_location());
	
	}
	
	/**
	 * File class' destructor.
	 */
	public function __destruct(){
		// Close ressource
		fclose($this->file);
	}

}
?>