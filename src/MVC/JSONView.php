<?php
/**
 * JSON View Abstraction
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */

namespace Apine\MVC;

use Apine\Core\Request;

/**
 * JSON View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @author François Allard <allard.f@kitaiweb.ca>
 * @package Apine\MVC
 */
final class JSONView extends View
{
    /**
     * Json File
     *
     * @var string
     */
    private $json_file;
    
    /**
     * JSONView constructor.
     *
     * @param array $a_json
     */
    public function __construct($a_json = array())
    {
        if (is_array($a_json) && count($a_json) > 0) {
            $this->setJsonFile($a_json);
        }
    }
    
    /**
     * Set Json File
     *
     * @param string|array $a_json
     *
     * @return string
     */
    public function setJsonFile($a_json)
    {
        $options = 0;
        $get = Request::get();
        
        if (isset($get['json_pretty'])) {
            $options |= JSON_PRETTY_PRINT;
        }
        
        if (is_string($a_json)) {
            // Verify if valid json array
            //$result = json_decode($a_json);
            json_decode($a_json);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->json_file = $a_json;
                $return = $a_json;
            } else {
                $return = null;
            }
        } else {
            if (is_object($a_json)) {
                $this->json_file = json_encode($a_json, $options);
                $return = $this->json_file;
            } else {
                if (is_array($a_json)) {
                    $this->json_file = json_encode($a_json, $options);
                    $return = $this->json_file;
                } else {
                    $return = null;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Send View to output
     */
    public function draw()
    {
        $this->setHeaderRule('Content-type: application/json');
        $this->applyHeaders();
        
        if ($this->json_file === null) {
            // Encode to JSON
            $this->setJsonFile($this->params);
        }
        
        print $this->json_file;
    }
    
    /**
     * Return the content of the view
     *
     * @return string
     */
    public function content()
    {
        if (is_null($this->json_file)) {
            $this->setJsonFile($this->params);
        }
        
        return $this->json_file;
    }
    
}