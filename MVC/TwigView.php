<?php

namespace Apine\MVC;

use Apine\Application\Application;
use Apine\Application\Translator;
use Apine\Exception\GenericException;
use Apine\MVC\Twig\TwigExtension;
use Apine\MVC\URLHelper;
use Apine\MVC\View;
use Apine\Session\SessionManager;

class TwigView extends View {

    /**
     * Path to layout file
     *
     * @var string
     */
    private $layout;
	
	/**
	 * Path to layout folder file
	 *
	 * @var string
	 */
	private $layout_path;

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
     * List of custom meta tags
     * @var array $_metatags
     */
    //private $_metatags;

    /**
     * List of stylesheets to include
     *
     * @var array
     */
    //private $_styles;

    /**
     * List of scripts to include
     *
     * @var array
     */
    //private $_scripts;

    /**
     * View's HTML Document
     *
     * @var string $content
     */
    private $content;
	
	/**
	 * List of HTML literal string to include
	 * @var array
	 */
	//private $literals;

    /**
     * Construct the HTML view
     *
     * @param string $a_title
     * @param string $a_view
     * @param string $a_layout
     */
    public function __construct($a_title = "", $a_view = "view", $a_layout = "layout") {
        parent::__construct();
        $this->title = $a_title;
        $this->set_view($a_view);
        $config = Application::get_instance()->get_config();

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
        if ($a_title != "") {
            $this->title = $a_title;
        }
    }

    /**
     * Append a meta tag to <head>
     *
     * @param array $a_properties
     */
    /*public function add_meta ($a_properties) {

        if (is_array($a_properties) && count($a_properties) >= 1) {

            $tag = "<meta";

            foreach ($a_properties as $name=>$value) {
                $tag .= " $name=\"$value\"";
            }

            $tag .= ">";
            $this->_metatags[] = $tag;
        }

    }*/

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
            $this->view = "$a_view.html";

            // Verify if the view file exists
            if (file_exists("views/$a_view.html")) {
                $this->view_path = "$path/views/";
            } else if (file_exists($location . "/Views/$a_view.html")) {
                $this->view_path =  "$location/Views/";
            } else if (file_exists("$a_view.html")) {
                $this->view_path = "";
            } else {
                throw new GenericException('View Not Found', 500);
            }
        }
    }

    /**
     * Set path to layout file
     *
     * @param string $a_layout
     * @throws GenericException
     */
    public function set_layout($a_layout) {
        if ($a_layout != "") {
            $location = Application::get_instance()->framework_location();
			$path = Application::get_instance()->include_path();
			$this->layout = "$a_layout.html";

            // Verify if the layout file exists
			if (file_exists("views/$a_layout.html")) {
				$this->layout_path = "$path/views/";
			} else if (file_exists("views/layouts/$a_layout.html")) {
                $this->layout_path = "$path/views/layouts/";
            } else if (file_exists($location . "/Views/$a_layout.html")) {
                $this->layout_path = "$location/Views/";
            } else if (file_exists("$a_layout.html")) {
                $this->layout_path = "";
            } else {
                throw new GenericException('Layout Not Found', 500);
            }
        }
    }

    /**
     * Append javascript script to view
     *
     * @param string $a_script URL to script
     */
    /*public function add_script($a_script) {
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
    }*/

    /**
     * Append stylesheet to view
     *
     * @param string $a_sheet URL to script
     */
    /*public function add_stylesheet($a_sheet) {
        if ($a_sheet!="") {
            if (file_exists("resources/public/css/$a_sheet.css")) {
                $this->_styles[] = URLHelper::resource("resources/public/css/$a_sheet.css");
            } else if (file_exists("$a_sheet.css")) {
                $this->_styles[] = URLHelper::resource("$a_sheet.css");
            }
        }
    }*/

    /**
     * Insert meta tag into the view
     *
     * @param string[] $metatags
     * @return string
     */
    /*public static function apply_meta ($metatags) {
        $output = "";

        if (count($metatags) > 0) {
            foreach ($metatags as $value) {
                $output .= $value . "\r\n";
            }
        }

        return $output;
    }*/

    /**
     * Insert script into the view
     *
     * @param string[] $scripts
     * @return string
     */
    /*public static function apply_scripts($scripts) {
        $output = "";

        if (count($scripts)>0) {
            foreach ($scripts as $value) {
                $output .= "<script src=\"$value\"></script>";
            }
        }

        return $output;
    }*/
	
	/**
	 * Inject literal HTML in the view
	 *
	 * @param string $a_literal
	 * @param string $a_zone
	 */
	/*public function add_html_literal ($a_literal, $a_zone = "default") {
		
		if($a_literal!="") {
			$this->literals[$a_zone][] = $a_literal;
		}
		
	}
	
	public static function apply_html_literals ($a_literals) {
		
		$output = "";
		
		if (count($a_literals)>0) {
			foreach ($a_literals as $literal) {
				$output .= $literal;
			}
		}
		
		return $output;
		
	}*/

    /**
     * Insert stylesheets into the view
     *
     * @param string[] $styles
     * @return string
     */
    /*public static function apply_stylesheets($styles) {
        $output = "";

        if (count($styles)>0) {
            foreach ($styles as $value) {
                $output .= "<link href=\"$value\" rel=\"stylesheet\" />";
            }
        }

        return $output;
    }*/

    public function draw() {
        // Apply headers

        if (is_null($this->content)) {
            $this->content();
        }

        print $this->content();
    }

    public function content() {
        /* TODO: Add rules
         *
         * Configuration : functions (Ex: {{ config('application', 'title') }})
         * Translations: functions (Ex: {{ translate('text', 'category') }}, {{ text|translate('category') }})
         * URLHelper : filters (Ex: {{ 'home/construction'|url_secure }}, {{ 'home/construction'|url }}, {{ 'resources/public/assets/favicon.ico'|url_resource }}, {{ url_secure('home/construction') }})
         * Dynamic Insertions (Script Tags, Style Tags, Meta Tags, ...) : variables (Ex: {{ apine.meta }}, {{ apine.style }}, {{ apine.script }}, {{ apine.view }}, {{ apine.title }})
         * Logged In User : global (Ex: {{ apine_user.get_username() }})
         * Execution Time : function (Ex: {{ execution_time() }})
         *
         */
        //$this->layout_path = null;
        
        $loader1 = new \Twig_Loader_Filesystem($this->view_path);
		$loader2 = new \Twig_Loader_Filesystem($this->layout_path);
		$loader = new \Twig_Loader_Chain([$loader1, $loader2]);
        $twig = new \Twig_Environment($loader, array(
			'cache' => $this->view_path . '/_cache',
			'auto-reload' => true,
			'debug' => (Application::get_instance()->get_mode() === APINE_MODE_DEVELOPMENT)
		));
        $twig->addExtension(new TwigExtension());
        $template = null;
		$language = Translator::language();
		
        $apine_array = array(
            'title' => $this->title,
			'mode' => Application::get_instance()->get_mode(),
			'https' => Application::get_instance()->get_use_https(),
			'secure' => Application::get_instance()->get_secure_session(),
			'language' => array(
				'code' => $language->code,
				'short' => $language->code_short,
				'name' => Translator::translation()->get("language","name")
			),
			/*'scripts' => $this->_scripts,
			'stylesheets' => $this->_styles,
			'metadata' => $this->_metatags,
			'literals' => $this->literals,*/
			'user' => (SessionManager::get_user()) ? SessionManager::get_user() : null,
			'version' => array(
				'framework' => Application::get_instance()->get_version()->framework(),
				'application' => Application::get_instance()->get_version()->application()
			),
			'layout' => (!is_null($this->layout)) ? array(
				'file' => (string) $this->layout,
				'path' => (string) $this->layout_path,
				'filepath' => (string) $this->layout_path.$this->layout
			) : null,
			'view' => (!is_null($this->view)) ? array(
				'file' => $this->view,
				'path' => $this->view_path,
				'filepath' => $this->view_path.$this->view
			) : null
        );

        /*if (is_null($this->_layout)) {
            $template = $twig->loadTemplate($this->_view);
        } else {
            $apine_array['view'] = $this->_view;
            $template = $twig->loadTemplate($this->_layout);
        }*/
	
		if (is_null($this->view)) {
			$template = $twig->loadTemplate($this->layout);
		} else {
			//$apine_array['layout'] = $this->layout;
			$template = $twig->loadTemplate($this->view);
		}

        $twig->addGlobal('apine', $apine_array);
        $this->content = $template->render($this->_params->get_all());
        return $this->content;
    }
}