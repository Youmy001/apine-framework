<?php
/**
 * Representation of the XML Element
 * This script contains a extenxion of a DOM Element
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\XML;

use \DOMNode;
use \DOMElement;
use \DOMDocument;
use \DOMAttr;
use \DOMComment;
use \DOMText;
use \Exception;

/**
 * Class Element
 * DOMNode and DOMElement encapsulation. Read and modify XML elements
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.om>
 * @package Apine\XML
 * @deprecated
 */
class Element extends DOMElement
{
    /**
     * Instance of the Document
     *
     * @var DOMDocument
     */
    private $document;
    
    /**
     * Instance of the Element
     *
     * @var DOMElement
     */
    private $element;
    
    /**
     * Element constructor.
     *
     * @param DOMDocument $a_document
     * @param DOMElement  $a_element
     */
    public function __construct(DOMDocument &$a_document, DOMElement &$a_element)
    {
        $this->document = $a_document;
        $this->element = $a_element;
    }
    
    /**
     * Add an attribute to the element
     *
     * @deprecated
     *
     * @param $a_attr
     *          Name of the attribute
     * @param $a_value
     *          Value of the attribute
     *
     * @return DOMAttr
     *          Attribute added
     */
    public function addAttribute($a_attr, $a_value)
    {
        return $this->setAttribute($a_attr, $a_value);
    }
    
    /**
     * Add a comment to the element
     *
     * @param string $a_comment
     *          Comment text
     *
     * @return DOMComment
     */
    public function addComment($a_comment)
    {
        $node = $this->document->createComment($a_comment);
        $node = $this->element->appendChild($node);
        
        return $node;
    }
    
    /**
     * Add text to the element
     *
     * @param string $a_content
     *          Text to append
     *
     * @return DOMText
     */
    public function addTextNode($a_content)
    {
        $text = $this->document->createTextNode($a_content);
        $this->element->appendChild($text);
        
        return $text;
    }
    
    /**
     * Append a node to the element
     *
     * @param DOMNode $node
     *          Nodo to append
     *
     * @return DOMNode
     */
    public function appendChild(DOMNode $node)
    {
        $node = $this->element->appendChild($node);
        
        return $node;
    }
    
    /**
     * Add an attribute to the element
     *
     * @param $a_name
     *          Name of the attribute
     * @param $a_value
     *          Value of the attribute
     *
     * @return DOMAttr
     *          Attribute added
     */
    public function setAttribute($a_name, $a_value)
    {
        $attr = $this->element->setAttribute($a_name, $a_value);
        
        return $attr;
    }
    
    /**
     * Fetch an attribute
     *
     * @param string $a_name
     *          Name of the attribute
     *
     * @return string
     *          Value of the attribute
     */
    public function getAttribute($a_name)
    {
        if ($this->element->hasAttribute($a_name)) {
            return $this->element->getAttribute($a_name);
        } else {
            return null;
        }
    }
    
    /**
     * Remove an attribute from the element
     *
     * @param string $a_name
     *          Name of the attribute
     *
     * @return boolean
     */
    public function removeAttribute($a_name)
    {
        return ($this->element->removeAttribute($a_name));
    }
    
    /**
     * Fetch the number of the line where the element begins
     *
     * @return integer
     */
    public function getLineNumber()
    {
        return $this->element->getLineNo();
    }
    
    /**
     * Verify if an attribute exists within the element
     *
     * @param string $a_name
     *          Name of the attribute
     *
     * @return bool
     */
    public function hasAttribute($a_name)
    {
        return $this->element->hasAttribute($a_name);
    }
    
    /**
     * Verify if the element has any attributes
     *
     * @return bool
     */
    public function hasAttributes()
    {
        return $this->element->hasAttributes();
    }
    
    /**
     * Verify if the element has child nodes
     *
     * @return bool
     */
    public function hasChildNodes()
    {
        return $this->element->hasChildNodes();
    }
    
    /**
     * Add a child node before another
     *
     * @param DOMNode $new_node
     *          New node
     * @param DOMNode $ref_node
     *          Node in front of which insert the new node
     *
     * @return DOMNode
     *          Node inserted
     * @throws Exception
     */
    public function insertBefore(DOMNode $new_node, DOMNode $ref_node = null)
    {
        try {
            $node = $this->element->insertBefore($new_node, $ref_node);
            
            return $node;
        } catch (Exception $e) {
            throw new Exception("DOM Error : " . $e->getMessage(), $e);
        }
    }
    
    /**
     * Remove a child node from the element
     *
     * @param DOMNode $old_node
     *          Node to remove
     *
     * @return DOMNode
     *          Node removed
     */
    public function removeChild(DOMNode $old_node)
    {
        $node = $this->element->removeChild($old_node);
        
        return $node;
    }
    
    /**
     * Replace a child node with another
     *
     * @param DOMNode $new_node
     *          New Node
     * @param DOMNode $old_node
     *          Node to replace
     *
     * @return DOMNode
     *          Node inserted
     */
    public function replaceChild(DOMNode $new_node, DOMNode $old_node)
    {
        $node = $this->element->replaceChild($new_node, $old_node);
        
        return $node;
    }
    
    /**
     * Clone the element
     *
     * @param bool $deep
     *          Whether to clone all descendant nodes
     *
     * @return DOMNode
     *          Cloned Element
     */
    public function cloneNode($deep = true)
    {
        $node = $this->element->cloneNode($deep);
        
        return $node;
    }
    
    /**
     * Define the value of the ID attribute of the element
     *
     * @param $id
     *          ID of the element
     *
     * @return DOMAttr
     */
    public function setId($id)
    {
        return $this->setAttribute("id", $id);
    }
    
    /**
     * Fetch the value of the ID attribute of the element
     *
     * @return string
     */
    public function getId()
    {
        if ($this->element->hasAttribute("id")) {
            return $this->getAttribute("id");
        } else {
            return null;
        }
    }
    
    /**
     * Extract the instance of the DOM Element
     *
     * @return DOMElement
     */
    public function getElement()
    {
        return $this->element;
    }
}