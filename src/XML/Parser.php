<?php
/**
 * XML Parser
 * This script contains a parsing utility for XML documents
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\XML;

use \DOMNodeList;

/**
 * Class Parser
 *
 * Representation of a XML document specialized in parsing.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\XML
 * @deprecated
 */
class Parser extends XML {

    /**
     * Parser constructor.
     */
    public function __construct() {
    	
    	parent::__construct();
    	
    }
    
    /**
     * Fetch an element by its ID
     *
     * @param string $a_id
     *          ID of the element
     * @return Element
     */
    public function getElementById($a_id) {
    	
    	if ($this->hasChildNodes()) {
    		foreach ($this->getChildNodes() as $node) {
    			if ($node->nodeType==XML_ELEMENT_NODE) {
    				if ($node->hasAttribute("id")&&$node->getAttribute("id")===$a_id) {
    					$element=$node;
    				}
    			}
            }
    	} else {
    		$element=null;
        }
        
       return $element;
       
    }
    
    /**
     * Fetch elements by attribute name
     *
     * @param string $a_attr
     *          Name of the attribute
     * @return DOMNodeList
     */
    public function getElementsByAttribute($a_attr) {

		$list = $this->DOM_document->createElement("list");
		
		if ($this->hasChildNodes()) {
			foreach ($this->getChildNodes() as $node) {
				if ($node->nodeType == XML_ELEMENT_NODE) {
					if ($node->hasAttribute($a_attr)) {
						$list->appendChild($node->cloneNode());
					}
				}
			}
		} else {
			$list = null;
		}
		
		return $list->childNodes;
	
	}
    
    /**
     * Fetch elements matching an attribute's value
     *
     * @param string $a_attr
     *          Name of the attribute
     * @param string $a_value
     *          Value of the attribute
     * @return DOMNodeList
     */
    public function getElementsByAttributeValue($a_attr, $a_value) {

		$list = $this->DOM_document->createElement("list");
		
		if ($this->hasChildNodes()) {
			foreach ($this->getChildNodes() as $node) {
				if ($node->nodeType == XML_ELEMENT_NODE) {
					if ($node->hasAttribute($a_attr) && $node->getAttribute($a_attr) === $a_value) {
						$list->appendChild($node->cloneNode(true));
					}
				}
			}
		} else {
			$list = null;
		}
		
		return $list->childNodes;
	
	}
    
    /**
     * Fetch elements from the name of the tag
     *
     * @param string $a_name
     *          Name of the tag
     * @return DOMNodeList
     *          Matching nodes
     */
    public function getElementByTagName($a_name) {

		$list = $this->DOM_document->getElementsByTagName($a_name);
		
		if ($list->length == 0) {
			$list = null;
		}
		
		return $list;
	
	}
    
    /**
     * Get line number of the element
     *
     * @return int
     */
    public function getLineNumber() {

		return $this->DOM_document->getLineNo();
	
	}
    
    /**
     * Verify if the document has attributes
     *
     * @return bool
     */
    public function hasAttributes() {

		return $this->DOM_document->hasAttributes();
	
	}
    
    /**
     * Verify if the document has child nodes
     *
     * @return bool
     */
    public function hasChildNodes() {

		return $this->DOM_document->hasChildNodes();
	
	}
    
    /**
     * Fetch nodes of the document
     *
     * @return DOMNodeList
     */
    public function getChildNodes() {

		if ($this->hasChildNodes()) {
			return $this->getElementByTagName('*');
		} else {
		    return null;
        }
	
	}
	
}