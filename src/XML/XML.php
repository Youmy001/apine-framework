<?php
/**
 * XML Document
 * This script contains the XML master class
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\XML;

use \DOMDocument;
use \Exception;

/**
 * Class XML
 * Representation of a XML-ish document
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\XML
 * @deprecated
 */
class XML
{
    /**
     * Path of the XML file
     *
     * @var string
     */
    private $xml_file;
    
    /**
     * Instance of the DOM
     *
     * @var DOMDocument
     */
    protected $DOM_document;
    
    /**
     * Is the file HTML
     *
     * @var bool
     */
    private $html;
    
    /**
     * XML constructor.
     */
    public function __construct()
    {
        $this->DOM_document = new DOMDocument("1.0", "UTF-8");
        $this->html = false;
    }
    
    /**
     * Open a XML document from a string
     *
     * @param string  $a_xml_string
     * @param integer $options
     */
    public function loadFromString($a_xml_string, $options)
    {
        $this->DOM_document->loadXML($a_xml_string, $options);
    }
    
    /**
     * Open a HTML documment from a string
     *
     * @param string $a_html_string
     */
    public function loadFromHtml($a_html_string)
    {
        $this->DOM_document = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->DOM_document->loadHTML($a_html_string,
            LIBXML_NOXMLDECL + LIBXML_HTML_NODEFDTD + LIBXML_HTML_NOIMPLIED + LIBXML_NOWARNING);
        libxml_clear_errors();
        $this->html = true;
    }
    
    /**
     * Open a XML document from a file
     *
     * @param string $a_xml_file
     *          Path of the XML file
     */
    public function loadFromFile($a_xml_file)
    {
        $this->setXmlFile($a_xml_file);
        $this->DOM_document->load($a_xml_file);
    }
    
    /**
     * Modify the path of the XML file
     *
     * @param string $a_xml_file
     */
    public function setXmlFile($a_xml_file)
    {
        $this->xml_file = $a_xml_file;
    }
    
    /**
     * Return the path of the XML file
     *
     * @return string
     */
    public function getXmlFile()
    {
        return $this->xml_file;
    }
    
    /**
     * Verify if the document is a HTML document
     *
     * @return bool
     */
    private function isHtml()
    {
        return $this->html;
    }
    
    /**
     * Return the content of the document as a string
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->isHtml()) {
            return $this->DOM_document->saveXML();
        } else {
            $this->DOM_document->encoding = 'UTF-8';
            
            return $this->DOM_document->saveHTML();
        }
    }
    
    /**
     * Save the document in a file
     *
     * @return boolean
     *          Retrun TRUE on success
     * @throws Exception
     *          If it fails to save the document in a file
     */
    public function save()
    {
        try {
            if ($this->getXmlFile() != "") {
                $save_state = $this->DOM_document->save($this->getXmlFile());
                if ($save_state == false) {
                    throw new Exception("An unknown error occured while saving the document.");
                }
            } else {
                throw new Exception("Impossible to save the document. A saving location hasn't been specified.");
            }
            
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . " - " . $e->getTraceAsString());
        }
    }
}
