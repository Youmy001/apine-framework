<?php
namespace Apine\MVC;

use Apine\File\File;

/**
 * File View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class FileView extends View {

	/**
	 * View File
	 *
	 * @var ApineFile
	 */
	private $_file;

	/**
	 * Construct File View
	 *
	 * @param ApineFile $a_file
	 */
	public function __construct(File $a_file=null) {

		parent::__construct();

		$this->set_file($a_file);

	}

	/**
	 * Set file
	 *
	 * @param string|ApineFile $a_file
	 */
	public function set_file($a_file=null) {

		if (!$a_file==null) {
			if (is_string($a_file)) {
				$this->_file = new File($a_file);
			} else if (is_a($a_file,'Apine\File\File')) {
				$this->_file = $a_file;
			}
		}

	}

	/**
	 * Send View to output
	 */
	public function draw() {

		$this->apply_headers();
		
		if (!is_null($this->_file)) {
			// Set headers
			header("Content-type: ".$this->_file->type());
			header("Content-Length: " . $this->_file->size());
			// Return the file
			$this->_file->output();
		}

	}

	/**
	 * Return the content of the view
	 *
	 * @return string
	 */
	public function content () {

		ob_start();
		$this->_file->output();
		$this->content = ob_get_contents();
		ob_end_clean();

		return $this->content;

	}

}