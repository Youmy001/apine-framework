<?php
/**
 * HTML View Abstraction with the TWIG templating engine
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\MVC;

use Apine\Application\Application;
use Apine\Application\Translator;
use Apine\Exception\GenericException;
use Apine\MVC\TwigExtension;
use Apine\MVC\URLHelper;
use Apine\MVC\View;
use Apine\Session\SessionManager;
use Apine\Utility\Types;

/**
 * HTML View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
final class HTMLView extends View {
	
	/**
	 * Path to view file
	 *
	 * @var string
	 */
	private $view;
	
	/**
	 * Path to view folder
	 *
	 * @var string
	 */
	private $view_path;
	
	/**
	 * Page Title
	 *
	 * @var string
	 */
	private $title;
	
	/**
	 * View's HTML Document
	 *
	 * @var string $content
	 */
	private $content;
	
	/**
	 * Use the Twig Template Engine
	 * @var boolean
	 */
	private $use_twig = false;
	
	/**
	 * List of HTML literal string to include
	 * @var array
	 */
	//private $literals;
	
	/**
	 * Construct the HTML view
	 *
	 * @param string $a_view Path to view file
	 * @param string $a_title
	 * @param array $params Data to pass to the view
	 */
	public function __construct($a_view = "", $a_title = "", $a_params = array()) {
		
		$this->title = $a_title;
		$this->set_view($a_view);
		
		if (is_array($a_params) && count($a_params) > 0) {
			$this->params = $a_params;
		}
	}
	
	/**
	 * Set page title
	 *
	 * @param string $a_title
	 */
	public function set_title($a_title) {
		if ($a_title != "") {
			$this->title = $a_title;
		}
	}
	
	/**
	 * Set path to view file
	 *
	 * @param string $a_view
	 * @throws GenericException
	 */
	public function set_view($a_view) {
		if ($a_view != "") {
			$location = Application::get_instance()->framework_location();
			$path = Application::get_instance()->include_path();
			//$this->view = "$a_view.html";
			
			// Verify if the view file exists
			if (file_exists("views/$a_view.twig")) {
				$this->view_path = "$path/views/";
				$this->view = "$a_view.twigh";
				$this->use_twig = true;
			} else if (file_exists($location . "/Views/$a_view.twig")) {
				$this->view_path =  "$location/Views/";
				$this->view = "$a_view.twig";
				$this->use_twig = true;
			} else if (file_exists("$a_view.twig")) {
				$this->view_path = "";
				$this->view = "$a_view.twig";
				$this->use_twig = true;
			} else if (file_exists("views/$a_view.html")) {
				$this->view_path = "$path/views/";
				$this->view = "$a_view.html";
			} else if (file_exists($location . "/Views/$a_view.html")) {
				$this->view_path =  "$location/Views/";
				$this->view = "$a_view.html";
			} else if (file_exists("$a_view.html")) {
				$this->view_path = "";
				$this->view = "$a_view.html";
			} else {
				throw new GenericException('View Not Found', 500);
			}
		}
	}
	
	public function draw() {
		// Apply headers
		
		if (is_null($this->content)) {
			$this->content();
		}
		
		print $this->content;
	}
	
	public function content() {
		
		if ($this->use_twig === true) {
			$path = Application::get_instance()->include_path();
			$language = Translator::language();
			$application = Application::get_instance();
			$apine_array = array(
				'application' => [
					'title' => $application->get_name(),
					'description' => $application->get_description(),
					'authors' => $application->get_authors(),
					'version' => $application->get_version()->application()
				],
				'title'   => $this->title,
				'debug'   => $application->is_debug_mode(),
				//'user' => (SessionManager::get_user()) ? SessionManager::get_user() : null,
				'version' => $application->get_version()->framework(),
				'view'    => (!is_null($this->view)) ? array(
					'file'     => $this->view,
					'path'     => $this->view_path,
					'filepath' => $this->view_path . $this->view
				) : null
			);
			
			if (is_a($language, 'Apine\Translation\TranslationLanguage')) {
				$apine_array['language'] = array(
					'code'  => $language->code,
					'short' => $language->code_short,
					'name'  => Translator::translation()->get("language", "name")
				);
			}
			
			$loader = new \Twig_Loader_Filesystem($this->view_path);
			$twig = new \Twig_Environment($loader, array(
				'cache'       => $path . '/views/_cache',
				'auto-reload' => true,
				'debug'       => ($application->is_debug_mode())
			));
			$twig->addExtension(new TwigExtension());
			$template = $twig->loadTemplate($this->view);
			$twig->addGlobal('apine', $apine_array);
			
			$this->content = $template->render($this->params);
		} else {
			$this->content = file_get_contents($this->view_path . $this->view);
		}
		
		return $this->content;
		
	}
}