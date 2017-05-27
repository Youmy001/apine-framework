<?php
/**
 * XML Parser
 * This script contains a writing utility for XML documents
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\XML;

use \DOMNode;
use \DOMAttr;
use \DOMText;
use \DOMComment;
use \Exception;

/**
 * Class Writter
 * Representation of a XML document specialized in document modification.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\XML
 * @deprecated
 */
class Writer extends Parser
{
    /**
     * Writer constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Create a new DOM attribute
     *
     * @param $a_attr
     *          Name of the attribute
     *
     * @return DOMAttr
     */
    public function createAttribute($a_attr)
    {
        $attribute = $this->DOM_document->createAttribute($a_attr);
        
        return $attribute;
    }
    
    /**
     * Create a new element
     *
     * @param string $name
     *          Tag name
     * @param string $value
     *
     * @return Element
     */
    public function createElement($name, $value = null)
    {
        $dom_element = $this->DOM_document->createElement($name, $value);
        $element = new Element($this->DOM_document, $dom_element);
        
        return $element;
    }
    
    /**
     * Create a text node
     *
     * @param string $content
     *          Text of the node
     *
     * @return DOMText
     */
    public function createTextNode($content)
    {
        $text = $this->DOM_document->createTextNode($content);
        
        return $text;
    }
    
    /**
     * Create a comment node
     *
     * @param string $comment
     *          Text of the comment
     *
     * @return DOMComment
     */
    public function addComment($comment)
    {
        $node = $this->DOM_document->createComment($comment);
        $node = $this->appendChild($node);
        
        return $node;
    }
    
    /**
     * Add a node to the document
     *
     * @param DOMNode $node
     *          Node to append
     *
     * @return DOMNode
     */
    public function appendChild(DOMNode $node)
    {
        $node = $this->DOM_document->appendChild($node);
        
        return $node;
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
            $node = $this->DOM_document->insertBefore($new_node, $ref_node);
            
            return $node;
        } catch (Exception $e) {
            throw new Exception("DOM Error : " . $e->getMessage());
        }
    }
    
    /**
     * Remove node from document
     *
     * @param DOMNode $old_node
     *          Node to remove
     *
     * @return DOMNode
     *          Node removed
     */
    public function removeChild(DOMNode $old_node)
    {
        $node = $this->DOM_document->removeChild($old_node);
        
        return $node;
    }
    
    /**
     * Replace a node with another
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
        $node = $this->DOM_document->replaceChild($new_node, $old_node);
        
        return $node;
    }
    
    /**
     * Clone the document
     *
     * @return DOMNode
     *          Cloned Element
     */
    public function cloneNode()
    {
        $node = $this->DOM_document->cloneNode(true);
        
        return $node;
    }
    
    /**
     * Import a node in the document
     *
     * @param DOMNode $new_node
     *          Node to import
     *
     * @return DOMNode
     *          Node inserted
     */
    public function importNode(DOMNode $new_node)
    {
        $node = $this->DOM_document->importNode($new_node, true);
        
        return $node;
    }
}