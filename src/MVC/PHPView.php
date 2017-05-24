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
final class PHPView extends View
{
    /**
     * Path to layout file
     *
     * @var string
     */
    private $layout;
    
    /**
     * Path to view file
     *
     * @var string
     */
    private $view;
    
    /**
     * Page Title
     *
     * @var string
     */
    private $title;
    
    /**
     * List of custom meta tags
     *
     * @var array $_metatags
     */
    private $metatags;
    
    /**
     * List of stylesheets to include
     *
     * @var array
     */
    private $styles;
    
    /**
     * List of scripts to include
     *
     * @var array
     */
    private $scripts;
    
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
    public function __construct($a_title = "", $a_view = "default", $a_layout = "default")
    {
        $this->scripts = array();
        
        $this->title = $a_title;
        $this->setView($a_view);
        
        $config = Application\Application::getInstance()->getConfig();
        
        if (!is_null($config)) {
            if ($a_layout == "default" && !is_null($config->get('runtime', 'default_layout'))) {
                $a_layout = $config->get('runtime', 'default_layout');
            }
        }
        
        $this->setLayout($a_layout);
    }
    
    /**
     * Set page title
     *
     * @param string $a_title
     */
    public function setTitle($a_title)
    {
        if ($a_title != "") {
            $this->_itle = $a_title;
        }
    }
    
    /**
     * Append a meta tag to <head>
     *
     * @param array $a_properties
     */
    public function addMeta($a_properties)
    {
        if (is_array($a_properties) && count($a_properties) >= 1) {
            
            $tag = "<meta";
            
            foreach ($a_properties as $name => $value) {
                $tag .= " $name=\"$value\"";
            }
            
            $tag .= ">";
            $this->metatags[] = $tag;
        }
    }
    
    /**
     * Insert meta tag into the view
     */
    public function applyMeta()
    {
        if (count($this->metatags) > 0) {
            foreach ($this->metatags as $value) {
                print($value . "\r\n");
            }
        }
    }
    
    /**
     * Set path to layout file
     *
     * @param string $a_layout
     */
    public function setLayout($a_layout)
    {
        if ($a_layout != "") {
            // Verify if the layout file exists
            if (file_exists("views/layouts/$a_layout.php")) {
                $this->layout = "views/layouts/$a_layout";
            } else {
                if (file_exists("$a_layout.php")) {
                    $this->layout = $a_layout;
                } else {
                    $location = Application\Application::getInstance()->frameworkLocation();
                    $this->layout = $location . '/Views/default_layout';
                }
            }
        }
    }
    
    /**
     * Set path to view file
     *
     * @param string $a_view
     */
    public function setView($a_view)
    {
        if ($a_view != "") {
            $location = Application\Application::getInstance()->frameworkLocation();
            
            // Verify if the view file exists
            if (file_exists("views/$a_view.php")) {
                $this->view = "views/$a_view";
            } else {
                if (file_exists($location . "/Views/$a_view.php")) {
                    $this->view = "$location/Views/$a_view";
                } else {
                    if (file_exists("$a_view.php")) {
                        $this->view = $a_view;
                    } else {
                        $this->view = $location . '/Views/default_view';
                    }
                }
            }
        }
    }
    
    /**
     * Append javascript script to view
     *
     * @param string $a_script URL to script
     */
    public function addScript($a_script)
    {
        if ($a_script != "") {
            if (file_exists("resources/public/scripts/$a_script.js")) {
                $this->scripts[] = URLHelper::resource("resources/public/scripts/$a_script.js");
            } else {
                if (file_exists("$a_script.js")) {
                    $this->scripts[] = URLHelper::resource("$a_script.js");
                } else {
                    if (stripos(@get_headers($a_script)[0], '200')) {
                        $this->scripts[] = $a_script;
                    } else {
                        throw new \RuntimeException('File "' . $a_script . '.js" not found');
                    }
                }
            }
        }
    }
    
    /**
     * Append stylesheet to view
     *
     * @param string $a_sheet URL to script
     */
    public function addStylesheet($a_sheet)
    {
        if ($a_sheet != "") {
            if (file_exists("resources/public/css/$a_sheet.css")) {
                $this->styles[] = URLHelper::resource("resources/public/css/$a_sheet.css");
            } else {
                if (file_exists("$a_sheet.css")) {
                    $this->styles[] = URLHelper::resource("$a_sheet.css");
                } else {
                    if (stripos(@get_headers($a_sheet)[0], '200')) {
                        $this->styles[] = $a_sheet;
                    } else {
                        throw new \RuntimeException('File "' . $a_sheet . '.css" not found');
                    }
                }
            }
        }
    }
    
    /**
     * Insert script into the view
     */
    public function applyScript()
    {
        
        if (count($this->scripts) > 0) {
            foreach ($this->scripts as $value) {
                print("<script src=\"$value\"></script>");
            }
        }
        
    }
    
    /**
     * Insert stylesheets into the view
     */
    public function applyStylesheet()
    {
        if (count($this->styles) > 0) {
            foreach ($this->styles as $value) {
                print("<link href=\"$value\" rel=\"stylesheet\" />");
            }
        }
        
    }
    
    /**
     * Send the view to output
     */
    public function draw()
    {
        $this->applyHeaders();
        
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
    public function content()
    {
        require_once Application\Application::getInstance()->frameworkLocation() . '/Includes/Functions.php';
        
        ob_start();
        include_once("$this->layout.php");
        $content = ob_get_contents();
        ob_end_clean();
        $this->content = $content;
        
        return $content;
    }
}