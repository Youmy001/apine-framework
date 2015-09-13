<?php
/**
 * File Resource Handler
 * This script contains an helper to manage files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * File Resource Handler
 * Manager wrapping PHP file method in an easy Object Oriented way
 */
class ApineFile {

	/**
	 * Ressource file
	 *
	 * @var resource
	 */
	protected $file;

	/**
	 * File location
	 *
	 * @var string
	 */
	private $location;

	/**
	 * File path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * File name
	 *
	 * @var string
	 */
	private $name;

	private $content;
	
	private $readonly;

	/**
	 * Construct the file handler
	 * Extract the file located at the provided location
	 *
	 * @param string $a_path
	 * @throws Exception
	 */
	public function __construct ($a_path, $a_readonly = false) {

		if (is_file($a_path)) {
			// Open File
			if ($a_readonly) {
				$this->file = fopen($a_path, "r");
			} else {
				$this->file = fopen($a_path, "c+");
			}
			$this->readonly = $a_readonly;
			$this->path = $a_path;
			$this->name = basename($a_path);
			$this->location = substr($this->path, 0, strripos($a_path, "/") + 1);
			$this->content = fread($this->file, filesize($a_path));
		} else {
			throw new Exception('File not found');
		}

	}

	/**
	 * Fetch file path
	 *
	 * @return string
	 */
	public function path () {

		return $this->path;

	}

	/**
	 * Fetch file location
	 *
	 * @return string
	 */
	public function directory () {

		return $this->location;

	}

	/**
	 * Fetch file size
	 *
	 * @return integer
	 */
	public function size () {

		return filesize($this->path);

	}

	/**
	 * Get file's mime type
	 *
	 * @return string
	 */
	public function type () {

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || !is_exec_available()) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $this->path);
			finfo_close($finfo);
		} else {
			$mime = exec("file -b --mime-type '".$this->path."'");
		}

		return $mime;

	}

	/**
	 * Get file's extention
	 * 
	 * @return string
	 */
	public function extention () {

		$dot_pos = strpos($this->name, ".");
		
		if ($dot_pos > 0) {
			$extention = substr($this->name, $dot_pos + 1);
		} else {
			$extention = $this->name;
		}

		return $extention;

	}

	/**
	 * Access file nane
	 * 
	 * @param string $a_name
	 * @return string
	 */
	public function name ($a_name = null) {

		if (!is_null($a_name)) {
			if (!strpos($this->name, ".")) {
				$this->name = $a_name . $this->extention();
			} else {
				$dot_pos = strpos($a_name, ".");
				$name = substr($a_name, 0, $dot_pos + 1);
				$this->name = $name . $this->extention();
			}
		}

		return $this->name;

	}

	/**
	 * Get file's content
	 * 
	 * @return mixed
	 */
	public function content () {

		return $this->content;

	}

	/**
	 * Add content to the file
	 * @param unknown $a_content
	 * @param string $a_append
	 */
	public function write ($a_content, $a_append = true) {

		if (!$this->readonly) {
			if ($a_append) {
				$this->content .= $a_content;
			} else {
				$this->content = $a_content;
			}
	
			ftruncate($this->file, 0);
			fwrite($this->file, $this->content);
		}

	}

	/**
	 * Save the file to its saving location.
	 * If no save locations are specified, it will save the file to
	 * its current location.
	 * @param string $a_path
	 * @return boolean
	 */
	public function save ($a_path = null) {

		if (!$this->readonly) {
			if ($a_path == null) {
				$directory = substr($this->path, 0, strripos($this->path, '/') + 1);
				$path = $directory . $this->name;
					
				$success = copy($this->path, $path);
					
				$this->path = $path;
				$this->location = $directory;
				fclose($this->file);
				$this->file = fopen($path, "c+");
			} else {
				// Verify is there's a filename in the path
				$file_name = substr($a_path, strripos($a_path, '/') + 1);
				$directory = substr($a_path, 0, strripos($a_path, '/') + 1);
					
				if ($file_name != '') {
					$this->name = $file_name;
					$path = $a_path;
				}else{
					$path = $directory . '/' . $this->name;
				}
					
				// If there's a filename, configure the class's name
				$success = rename($this->path, $path);
				$this->path = $path;
				$this->location = $directory;
				fclose($this->file);
				$this->file = fopen($path, "c+");
			}
			
			return $success;
		}

	}

	/**
	 * Move the file to another location
	 * 
	 * @return boolean
	 */
	public function move($a_move_path) {

		if (!$this->readonly) {
			if (is_null($a_move_path)) {
				return $this->save($this->location);
			} else {
				return $this->save($a_move_path);
			}
		}

	}

	/**
	 * Output file to output stream
	 */
	public function output() {

		readfile($this->path);

	}

	/**
	 * Fetch resource handler
	 * 
	 * @return resource
	 */
	public function handle() {

		return $this->file;

	}

	/**
	 * Remove file from disk.
	 * This action literaly erases the ressource from the hard drive.
	 */
	public function delete () {

		if (!$this->readonly) {
			unlink($this->path);
		}

	}

	/**
	 * File class' destructor.
	 */
	public function __destruct() {

		// Close ressource
		fclose($this->file);

	}

}