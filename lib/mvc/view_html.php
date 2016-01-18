<?php

/**
 * HTML View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class ApineHTMLView extends ApineView {

	/**
	 * Path to layout file
	 *
	 * @var string
	 */
	private $_layout;

	/**
	 * Path to view file
	 *
	 * @var string
	 */
	private $_view;

	/**
	 * Page Title
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * List of stylesheets to include
	 * 
	 * @var array
	 */
	private $_styles;

	/**
	 * List of scripts to include
	 *
	 * @var array
	 */
	private $_scripts;

	/**
	 * View's HTML Document
	 *
	 * @var string $content
	 */
	private $content;

	/**
	 * Construct the HTML view
	 *
	 * @param string $a_title
	 * @param string $a_view
	 * @param string $a_layout
	 */
	public function __construct($a_title = "", $a_view = "default", $a_layout = "default") {

		parent::__construct();
		$this->_scripts = array();

		$this->_title=$a_title;
		$this->set_view($a_view);

		if ($a_layout == "default" && !is_null(ApineConfig::get('application', 'default_layout'))) {
			$a_layout = ApineConfig::get('application', 'default_layout');
		}
		$this->set_layout($a_layout);

	}

	/**
	 * Set page title
	 *
	 * @param string $a_title
	 */
	public function set_title($a_title) {

		if ($a_title!="") {
			$this->_title=$a_title;
		}

	}

	/**
	 * Set path to layout file
	 *
	 * @param string $a_layout
	 */
	public function set_layout($a_layout) {

		if ($a_layout!="") {
			// Verify if the layout file exists
			if (file_exists("views/layouts/$a_layout.php")) {
				$this->_layout=$a_layout;
			} else {
				$this->_layout='default';
			}
		}

	}

	/**
	 * Set path to view file
	 *
	 * @param string $a_view
	 */
	public function set_view($a_view) {

		if ($a_view!="") {
			// Verify if the view file exists
			if (file_exists("views/$a_view.php")) {
				$this->_view=$a_view;
			} else {
				$this->_view='default';
			}
		}

	}
	
	/**
	 * Append javascript script to view
	 *
	 * @param string $a_script URL to script
	 */
	public function add_script($a_script) {
	
		if ($a_script!="") {
			if (file_exists("resources/public/scripts/$a_script.js")) {
				$this->_scripts[] = ApineURLHelper::resource("resources/public/scripts/$a_script.js");
			}
		}
	
	}

	/**
	 * Append stylesheet to view
	 *
	 * @param string $a_sheet URL to script
	 */
	public function add_stylesheet($a_sheet) {

		if ($a_script!="") {
			if (file_exists("resources/public/css/$a_script.css")) {
				$this->_styles[] = ApineURLHelper::resource("resources/public/css/$a_script.css");
			}
		}

	}

	/**
	 * Insert script into the view
	 */
	public function apply_script() {

		if (count($this->_scripts)>0) {
			foreach ($this->_scripts as $value) {
				print("<script src=\"$value\"></script>");
			}
		}

	}
	
	/**
	 * Insert stylesheets into the view
	 */
	public function apply_stylesheet() {
	
		if (count($this->_styles)>0) {
			foreach ($this->_styles as $value) {
				print("<link href=\"$value\" rel=\"stylesheet\" />");
			}
		}
	
	}

	/**
	 * Send the view to output
	 */
	public function draw() {

		$this->apply_headers();

		if (is_null($this->content)) {
			$this->content();
		}

		print $this->content;

	}

	/**
	 * Return the content of the view
	 *
	 * @return string
	 */
	public function content() {

		ob_start();
		include_once("views/layouts/$this->_layout.php");
		$content = ob_get_contents();
		ob_end_clean();
		//die($content);
		$this->content = $content;

		return $content;

	}
}