<?php
namespace Apine\XML;

/*
/ Projet Marie-Delice 
/ Classe modèle d'écriture de XML pour l'application Web
/ License : 
/ Auteurs: Marc-André Douville Auger, Véronique Blais, Tommy Teasdale
*/

/*************************************************
 * Classe Writer
 * ---------------
 * Encapsulation des classes DOM_Document, DOM_Node et 
 * DOM_Element de PHP
 *
 * Cette classe permet de modifier et écrire un
 * document XML. 
 *************************************************/
class Writer extends Parser {
    
    /*********************************************
     * Méthode __construct
     * ---------------
     * Constructeur de la classe Writer
     * 
     * ENTRIES
     *    null
     * RETURN
     *    null
     *********************************************/
	public function __construct() {
		
		parent::__construct(); // Appelle à la fois le contructeur de Parser et XML
		
    }
    
    /*********************************************
     * Méthode createAttribute
     * ---------------
     * Créer un nouvel Attribut au document
     * 
     * ENTRIES
     *    STRING $a_attir        Nom de l'attibut
     * RETURN
     *    DOMAttr $attribute     Attribute créé
     *********************************************/
    public function createAttribute($a_attr) {
    	
    	$attribute=$this->DOM_document->createAttribute($a_attr);
    	return $attribute;
    	
    }
    
    /*********************************************
     * Méthode createElement
     * ---------------
     * Créer un nouvel Element au docuemnt
     * 
     * ENTRIES
     *    STRING $name        Nom de l'élément
     *    STRING $value       Valeur de l'élément
     * RETURN
     *    Element $element    Element créé
     *********************************************/
    public function createElement($name, $value = null){

		$element = new Element($this->DOM_document, ($this->DOM_document->createElement($name, $value)));
		return $element;
	
	}
    
    /*********************************************
     * Méthode createTextNode
     * ---------------
     * Ajouter du texte au document
     * 
     * ENTRIES
     *    STRING $content   Texte à ajouter
     * RETURN
     *    DOMTextNode $text    Node du texte
     *********************************************/
    public function createTextNode($content) {
    	
    	$text=$this->DOM_document->createTextNode($content);
    	return $text;
    	
    }
    
    /*********************************************
     * Méthode addComment
     * ---------------
     * Ajouter un commentaire à l'élément
     * 
     * ENTRIES
     *    STRING $a_comment   Texte du commentaire
     * RETURN
     *    DOMComment $node    Node du commentaire ajouté
     *********************************************/
    public function addComment($comment) {
    	
    	$node=$this->DOM_document->createComment($comment);
    	$node=$this->appendChild($node);
    	return $node;
    	
    }
    
    /*********************************************
     * Méthode appendChild
     * ---------------
     * Ajouter un node enfant au document
     * 
     * ENTRIES
     *    DOMNode $node       Node à ajouter
     * RETURN
     *    DOMNode $node       Node ajouté
     *********************************************/
    public function appendChild(DOMNode $node) {
    	
    	$node=$this->DOM_document->appendChild($node);
    	return $node;
    	
    }
    
    /*********************************************
     * Méthode insertBefore
     * ---------------
     * Ajouter un node enfant avant un autre
     * 
     * ENTRIES
     *    DOMNode $new_node   Node à ajouter
     *    DOMNode $ref_node   Node avant lequel insérer
     * RETURN
     *    DOMNode $node       Node ajouté
     *********************************************/
    public function insertBefore(DOMNode $new_node, DOMNode $ref_node=null) {
    	
    	try {
    		$node=$this->DOM_document->insertBefore($new_node,$ref_node);
    		return $node;
    	} catch(Exception $e) {
    		throw new Exception("Erreur de DOM : ".$e->getMessage());
        }
        
    }
    
    /*********************************************
     * Méthode removeChild
     * ---------------
     * Retirer un node enfant du document
     * 
     * ENTRIES
     *    DOMNode $old_node   Node à retirer
     * RETURN
     *    DOMNode $node       Node rétiré
     *********************************************/
    public function removeChild(DOMNode $old_node) {
    	
    	$node=$this->DOM_document->removeChild($old_node);
    	return $node;
    	
    }
    
    /*********************************************
     * Méthode replaceChild
     * ---------------
     * Remplacer un node enfant avec un autre
     * 
     * ENTRIES
     *    DOMNode $new_node   Node à ajouter
     *    DOMNode $old_node   Node à remplacer
     * RETURN
     *    DOMNode $node       Node ajouté
     *********************************************/
    public function replaceChild(DOMNode $new_node, DOMNode $old_node) {
    	
    	$node=$this->DOM_document->replaceChild($new_node,$old_node);
    	return $node;
    	
    }
    
    /*********************************************
     * Méthode cloneNode
     * ---------------
     * Cloner le document
     * 
     * ENTRIES
     *    BOOLEAN $deep       Si il faut copier l'arbre au complet
     * RETURN
     *    DOMNode $node       Node cloné
     *********************************************/
    public function cloneNode() {
    	
    	$node=$this->DOM_document->cloneNode();
    	return $node;
    	
    }
    
    /*********************************************
     * Méthode importNode
     * ---------------
     * Importer un node enfant
     * 
     * ENTRIES
     *    DOMNode $new_node   Node à importer
     * RETURN
     *    DOMNode $node       Node importé
     *********************************************/
    public function importNode(DOMNode $new_node) {
    	
    	$node=$this->DOM_document->importNode($new_node,true);
    	return $node;
    	
    }

}