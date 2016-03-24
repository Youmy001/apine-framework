<?php
namespace Apine\XML;

/*
/ Projet Marie-Delice 
/ Classe modèle de lecture de XML pour l'application Web
/ License : 
/ Auteurs: Marc-André Douville Auger, Véronique Blais, Tommy Teasdale
*/

/*************************************************
 * Classe Parser
 * ---------------
 * Encapsulation des classes DOM_Document, DOM_Node et 
 * DOM_Element de PHP
 *
 * Cette classe permet de lire un document XML. 
 *************************************************/
class Parser extends XML {
    
    /*********************************************
     * Méthode __construct
     * ---------------
     * Constructeur de la classe Parser
     * 
     * ENTRIES
     *    null
     * RETURN
     *    null
     *********************************************/
    public function __construct() {
    	
    	parent::__construct();
    	
    }
    
    /*********************************************
     * Méthode getElementById
     * ---------------
     * Obtenir un élément avec un Id
     * 
     * ENTRIES
     *    INTEGER $a_id       Id de l'élément
     * RETURN
     *    Element $element    Element correspondant
     *********************************************/
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
    
    /*********************************************
     * Méthode getElementByAttribute
     * ---------------
     * Obtenir les éléments avec un nom d'attribut
     * 
     * ENTRIES
     *    STRING $a_attr      Nom de l'attribut
     * RETURN
     *    DOMNodeList         Liste d'éléments correspondant
     *********************************************/
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
    
    /*********************************************
     * Méthode getElementByAttributeValue
     * ---------------
     * Obtenir les éléments avec le nom et la valeur d'attribut
     * 
     * ENTRIES
     *    STRING $a_attr      Nom de l'attribut
     *    STRING $a_value     Valeur de l'attribut
     * RETURN
     *    DOMNodeList         Liste d'éléments correspondant
     *********************************************/
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
    
    /*********************************************
     * Méthode getElementByTagName
     * ---------------
     * Obtenir les éléments avec le nom de l'élément
     * 
     * ENTRIES
     *    STRING $a_name      Nom de l'élément
     * RETURN
     *    DOMNodeList $list   Liste d'éléments correspondant
     *********************************************/
    public function getElementByTagName($a_name) {

		$list = $this->DOM_document->getElementsByTagName($a_name);
		
		if ($list->length == 0) {
			$list = null;
		}
		
		return $list;
	
	}
    
     /*********************************************
     * Méthode getLineNumber
     * ---------------
     * Obtenir le numéro de ligne de l'élément
     * 
     * ENTRIES
     *    null
     * RETURN
     *    INTEGER             Numéro de line
     *********************************************/
    public function getLineNumber() {

		return $this->DOM_document->getLineNo();
	
	}
    
    /*********************************************
     * Méthode hasAttributes
     * ---------------
     * Vérifier si il y a des attributs
     * 
     * ENTRIES
     *    nul
     * RETURN
     *    BOOLEAN             Si il y a des attributs
     *********************************************/
    public function hasAttributes() {

		return $this->DOM_document->hasAttributes();
	
	}
    
    /*********************************************
     * Méthode hasChildNodes
     * ---------------
     * Vérifier si il y a des nodes enfants
     * 
     * ENTRIES
     *    nul
     * RETURN
     *    BOOLEAN             Si il y a des enfants
     *********************************************/
    public function hasChildNodes() {

		return $this->DOM_document->hasChildNodes();
	
	}
    
    /*********************************************
     * Méthode getChildNodes
     * ---------------
     * Obtenir les nodes enfants
     * 
     * ENTRIES
     *    nul
     * RETURN
     *    DOMNodeList $list   Liste d'éléments correspondant
     *********************************************/
    public function getChildNodes() {

		if ($this->hasChildNodes()) {
			return $this->getElementByTagName('*');
		}
	
	}
	
}