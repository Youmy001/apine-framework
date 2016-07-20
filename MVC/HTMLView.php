<?php
namespace Apine\MVC;

use Apine\Application as Application;
use Apine\Exception\GenericException;
use TinyTemplate\Template;
use TinyTemplate\Layout;
use TinyTemplate\Engine;

/**
 * HTML View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class HTMLView extends View {

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
	 * Construct the HTML view
	 *
	 * @param string $a_title
	 * @param string $a_view
	 * @param string $a_layout
	 */
	public function __construct($a_title = "", $a_view = "view", $a_layout = "layout") {

		parent::__construct();
		$this->_scripts = array();

		$this->_title=$a_title;
		
		$this->set_view($a_view);
		
		$config = Application\Application::get_instance()->get_config();
		
		if (!is_null($config)) {
			if ($a_layout == "layout" && !is_null($config->get('runtime', 'default_layout'))) {
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
	 * Set path to layout file
	 *
	 * @param string $a_layout
	 */
	public function set_layout($a_layout) {
		
		if ($a_layout != "") {
			$location = Application\Application::get_instance()->framework_location();
			
			// Verify if the layout file exists
			if (file_exists("views/layouts/$a_layout.html")) {
				$this->_layout = new Layout("views/layouts/$a_layout.html");
			} else if (file_exists($location . "/Views/$a_layout.html")) {
				$this->_layout = new Layout($location . "/Views/$a_layout.html");
			} else if (file_exists("$a_layout.html")) {
				$this->_layout = new Layout("$a_layout.html");
			} else {
				throw new GenericException('Layout Not Found', 500);
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
			$path = Application\Application::get_instance()->include_path();
			
			// Verify if the view file exists
			if (file_exists("views/$a_view.html")) {
				$view = "$path/views/$a_view.html";
			} else if (file_exists($location . "/Views/$a_view.html")) {
				$view = "$location/Views/$a_view.html";
			} else if (file_exists("$a_view.html")) {
				$view = "$a_view.html";
			} else {
				throw new GenericException('View Not Found', 500);
			}
			
			$this->_view = new Template($view);
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
			} else {
				$headers = @get_headers($a_script);
				if (stripos($headers[0], '200')) {
					$this->_scripts[] = $a_script;
				}
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
			}
		}

	}
	
	/**
	 * Insert meta tag into the view
	 */
	public static function apply_meta ($metatags) {
	
		$output = "";
		
		if (count($metatags) > 0) {
			foreach ($metatags as $value) {
				$output .= $value . "\r\n";
			}
		}
		
		return $output;
	
	}

	/**
	 * Insert script into the view
	 */
	public static function apply_scripts($scripts) {
		
		$output = "";

		if (count($scripts)>0) {
			foreach ($scripts as $value) {
				$output .= "<script src=\"$value\"></script>";
			}
		}
		
		return $output;

	}
	
	/**
	 * Insert stylesheets into the view
	 */
	public static function apply_stylesheets($styles) {
		
		$output = "";
		
		if (count($styles)>0) {
			foreach ($styles as $value) {
				$output .= "<link href=\"$value\" rel=\"stylesheet\" />";
			}
		}
		
		return $output;
	
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

		$config = \Apine\Application\Application::get_instance()->get_config();
		
		if (!is_null($config)) {
			if (\Apine\Session\SessionManager::is_logged_in()) {
				$user_array = array();
				$apine_user = \Apine\Session\SessionManager::get_user();
				$user_array['id'] = $apine_user->get_id();
				$user_array['username'] = $apine_user->get_username();
				$user_array['password'] = $apine_user->get_password();
				$user_array['type'] = $apine_user->get_type();
				$user_array['email'] = $apine_user->get_email_address();
				$user_array['register_date'] = $apine_user->get_register_date();
				$user_array['groups'] = array();
				
				$properties = $apine_user->get_property_all();
				
				if (is_array($properties)) {
					foreach ($properties as $name => $value) {
						$user_array["property_" . $name] = $value->get_value();
					}
				}
				
				foreach ($apine_user->get_group() as $group) {
					$user_array['group_' . $group->get_id()] = true;
					$user_array['groups'][$group->get_id()] = $group->get_id();
				}
			} else {
				$user_array = false;
			}
		} else {
			$user_array = false;
		}
		
		Engine::instance()->add_data(array(
				'apine_user' => $user_array,
				'apine_application_https' => Application\Application::get_instance()->get_use_https(),
				'apine_application_mode' => Application\Application::get_instance()->get_mode(),
				'apine_application_secure' => Application\Application::get_instance()->get_secure_session(),
				'apine_view_metatags' => $this->_metatags,
				'apine_view_scripts' => $this->_scripts,
				'apine_view_stylesheets' => $this->_styles,
				"apine_view_title" => $this->_title
		));
		Engine::instance()->add_data($this->_params->get_all());
		$this->content = Engine::instance()->process($this->_view, $this->_layout);

		return $this->content;

	}
}