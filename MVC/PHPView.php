<?php
/**
 * PHP View Abstraction
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\MVC;

use Apine\Application as Application;

/**
 * PHP View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
final class PHPView extends View {

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
	 * List of custom meta tags
	 * @var array $_metatags
	 */
	private $_metatags;
	
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
	 * Construct the PHP view
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

		$config = Application\Application::get_instance()->get_config();
		
		if (!is_null($config)) {
			if ($a_layout == "default" && !is_null($config->get('runtime', 'default_layout'))) {
				$a_layout = $config->get('runtime', 'default_layout');
			}
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
	 * Append a meta tag to <head>
	 *  
	 * @param array $a_properties
	 */
	public function add_meta ($a_properties) {
		
		if (is_array($a_properties) && count($a_properties) >= 1) {
			
			$tag = "<meta";
			
			foreach ($a_properties as $name=>$value) {
				$tag .= " $name=\"$value\"";
			}
			
			$tag .= ">";
			$this->_metatags[] = $tag;
		}
		
	}
	
	/**
	 * Insert meta tag into the view
	 */
	public function apply_meta () {
		
		if (count($this->_metatags) > 0) {
			foreach ($this->_metatags as $value) {
				print($value . "\r\n");
			}
		}
		
	}

	/**
	 * Set path to layout file
	 *
	 * @param string $a_layout
	 */
	public function set_layout($a_layout) {

		if ($a_layout != "") {
			// Verify if the layout file exists
			if (file_exists("views/layouts/$a_layout.php")) {
				$this->_layout = "views/layouts/$a_layout";
			} else if (file_exists("$a_layout.php")) {
				$this->_layout = $a_layout;
			} else {
				$location = Application\Application::get_instance()->framework_location();
				$this->_layout = $location . '/Views/default_layout';
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
			$location = Application\Application::get_instance()->framework_location();
			
			// Verify if the view file exists
			if (file_exists("views/$a_view.php")) {
				$this->_view = "views/$a_view";
			} else if (file_exists($location . "/Views/$a_view.php")) {
				$this->_view = "$location/Views/$a_view";
			} else if (file_exists("$a_view.php")) {
				$this->_view = $a_view;
			} else {
				$this->_view = $location . '/Views/default_view';
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
				$this->_scripts[] = URLHelper::resource("resources/public/scripts/$a_script.js");
			} else if (file_exists("$a_script.js")) {
				$this->_scripts[] = URLHelper::resource("$a_script.js");
			} else if (stripos(@get_headers($a_script)[0], '200')) {
				$this->_scripts[] = $a_script;
			} else {
				throw new \RuntimeException('File "' . $a_script . '.js" not found');
			}
		}
	
	}

	/**
	 * Append stylesheet to view
	 *
	 * @param string $a_sheet URL to script
	 */
	public function add_stylesheet($a_sheet) {
		
		if ($a_sheet!="") {
			if (file_exists("resources/public/css/$a_sheet.css")) {
				$this->_styles[] = URLHelper::resource("resources/public/css/$a_sheet.css");
			} else if (file_exists("$a_sheet.css")) {
				$this->_styles[] = URLHelper::resource("$a_sheet.css");
			} else if (stripos(@get_headers($a_sheet)[0], '200')) {
				$this->_styles[] = $a_sheet;
			} else {
				throw new \RuntimeException('File "' . $a_sheet . '.css" not found');
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
		
		require_once Application\Application::get_instance()->framework_location() . '/Includes/Functions.php';

		ob_start();
		include_once("$this->_layout.php");
		$content = ob_get_contents();
		ob_end_clean();
		$this->content = $content;

		return $content;

	}
}