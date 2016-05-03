<?php
namespace Apine\MVC;

/**
 * JSON View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class JSONView extends View {

	/**
	 * Json File
	 *
	 * @var string
	 */
	private $_json_file;

	/**
	 * Set Json File
	 *
	 * @param string|array $a_json
	 * @return string
	 */
	public function set_json_file($a_json) {

		if (is_string($a_json)) {
			// Verify if valid json array
			$result = json_decode($a_json);
				
			if (json_last_error() === JSON_ERROR_NONE) {
				$this->_json_file=$a_json;
				$return=$a_json;
			}else{
				$return=null;
			}
		} else if (is_object($a_json)) {
			$this->_json_file=json_encode($a_json);
			$return=$this->_json_file;
		} else if (is_array($a_json)) {
			$this->_json_file=json_encode($a_json);
			$return=$this->_json_file;
		} else {
			$return=null;
		}

		return $return;

	}

	/**
	 * Send View to output
	 */
	public function draw() {

		$this->set_header_rule('Content-type: application/json');
		$this->apply_headers();

		if($this->_json_file===null){
			// Encode Objects to Array
				
			//print_r($this->_params->get_all());
			// Encode to JSON
			$this->set_json_file($this->_params->get_all());
		}

		print $this->_json_file;

	}

	/**
	 * Return the content of the view
	 *
	 * @return string
	 */
	public function content () {

		if (is_null($this->_json_file)) {
			$this->set_json_file($this->_params->get_all());
		}

		return $this->_json_file;

	}

}