<?php
namespace Apine\XML;

use \DOMElement;
use \DOMDocument;

/*
/ Projet Marie-Delice 
/ Classe modèle d'élément XML pour l'application Web
/ License : 
/ Auteurs: Marc-André Douville Auger, Véronique Blais, Tommy Teasdale
*/

/*************************************************
 * Classe Element
 * ---------------
 * Encapsulation des classes DOM_Node et 
 * DOM_Element de PHP
 *
 * Cette classe permet de lire et modifier un 
 * élément XML. 
 *************************************************/
class Element extends DOMElement {
    
    private $document;    // Instance du DOM
    
    private $element;    // Instance de l'élément DOM
    
    /*********************************************
     * Méthode __construct
     * ---------------
     * Constructeur de la classe Element
     * 
     * ENTRIES
     *    DOMDocument $a_document  Instance du DOM
     *    DOMElement $a_element    Instance de l'élément DOM
     * RETURN
     *    null
     *********************************************/
    public function __construct(DOMDocument &$a_document, DOMElement &$a_element) {

		$this->document = $a_document;
		$this->element = $a_element;
	
	}
    
    /*********************************************
     * Méthode addAttribute
     * ---------------
     * Ajouter un attibut à l'élément
     * 
     * ENTRIES
     *    STRING $a_attr      Nom de l'attribut
     *    STRING $a_value     Valeur de l'attribut
     * RETURN
     *    DOMAttr             Attribut ajouté
     *********************************************/
    public function addAttribute($a_attr, $a_value) {

		return $this->setAttribute($a_attr, $a_value);
	
	}
    
    /*********************************************
     * Méthode addComment
     * ---------------
     * Ajouter un commentaire à l'élément
     * 
     * ENTRIES
     *    STRING $a_comment   Texte du commentaire
     * RETURN
     *    DOMComment $node       Node du commentaire ajouté
     *********************************************/
    public function addComment($a_comment) {

		$node = $this->document->createComment($a_comment);
		$node = $this->element->appendChild($node);
		return $node;
	
	}
    
    /*********************************************
     * Méthode addTextNode
     * ---------------
     * Ajouter du texte à l'élément
     * 
     * ENTRIES
     *    STRING $a_content   Texte à ajouter
     * RETURN
     *    DOMTextNode $node       Node du texte ajouté
     *********************************************/
    public function addTextNode($a_content) {

		$text = $this->DOM_document->createTextNode($a_content);
		$this->element->appendChild($text);
		return $text;
	
	}
    
    /*********************************************
     * Méthode appendChild
     * ---------------
     * Ajouter un node enfant à l'élément
     * 
     * ENTRIES
     *    DOMNode $node       Node à ajouter
     * RETURN
     *    DOMNode $node       Node ajouté
     *********************************************/
    public function appendChild(DOMNode $node) {

		$node = $this->element->appendChild($node);
		return $node;
	
	}
    
    /*********************************************
     * Méthode setAttribute
     * ---------------
     * Ajouter un attibut à l'élément
     * 
     * ENTRIES
     *    STRING $a_name      Nom de l'attribut
     *    STRING $a_value     Valeur de l'attribut
     * RETURN
     *    DOMAttr $attr       Attribut ajouté
     *********************************************/
    public function setAttribute($a_name, $a_value) {

		$attr = $this->element->setAttribute($a_name, $a_value);
		return $attr;
	
	}
    
    /*********************************************
     * Méthode getAttribute
     * ---------------
     * Obtenir l'attibut d'un élément
     * 
     * ENTRIES
     *    STRING $a_name      Nom de l'attribut
     * RETURN
     *    STRING              Valeur de l'attribut de l'élément
     *********************************************/
    public function getAttribute($a_name) {

		if ($this->element->hasAttribute($a_name)) {
			return $this->element->getAttribute($a_name);
		}
	
	}
    
    /*********************************************
     * Méthode removeAttribute
     * ---------------
     * Retirier un attibut de l'élément
     * 
     * ENTRIES
     *    STRING $a_name      Nom de l'attribut
     * RETURN
     *    DOMAttr             Attribut supprimé
     *********************************************/
    public function removeAttribute($a_name) {

		return ($this->element->removeAttribute($a_name));
	
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

		return $this->element->getLineNo();
	
	}
    
    /*********************************************
     * Méthode hasAttribute
     * ---------------
     * Vérifier si un attribut existe
     * 
     * ENTRIES
     *    STRING $a_name      Nom de l'attribut
     * RETURN
     *    BOOLEAN             Si l'attribut existe
     *********************************************/
    public function hasAttribute($a_name) {

		return $this->element->hasAttribute($a_name);
	
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

		return $this->element->hasAttributes();
	
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

		return $this->element->hasChildNodes();
	
	}

	/**
	 * *******************************************
	 * Méthode insertBefore
	 * ---------------
	 * Ajouter un node enfant avant un autre
	 *
	 * ENTRIES
	 * 		DOMNode $new_node 	Node à ajouter
	 * 		DOMNode $ref_node 	Node avant lequel insérer
	 * RETURN
	 * 		DOMNode $node 		Node ajouté
	 * *******************************************
	 */
	public function insertBefore(DOMNode $new_node, DOMNode $ref_node = null){

		try{
			$node = $this->element->insertBefore($new_node, $ref_node);
			return $node;
		}catch(Exception $e){
			throw new Exception("Erreur de DOM : " . $e->getMessage());
		}
	
	}
    
    /*********************************************
     * Méthode removeChild
     * ---------------
     * Retirer un node enfant de l'élément
     * 
     * ENTRIES
     *    DOMNode $old_node   Node à retirer
     * RETURN
     *    DOMNode $node       Node rétiré
     *********************************************/
	public function removeChild(DOMNode $old_node) {

		$node = $this->element->removeChild($old_node);
		return $node;
	
	}

	/**
	 * *******************************************
	 * Méthode replaceChild
	 * ---------------
	 * Remplacer un node enfant avec un autre
	 *
	 * ENTRIES
	 * 		DOMNode $new_node 	Node à ajouter
	 * 		DOMNode $old_node 	Node à remplacer
	 * RETURN
	 * 		DOMNode $node 		Node ajouté
	 * *******************************************
	 */
	public function replaceChild(DOMNode $new_node, DOMNode $old_node) {

		$node = $this->element->replaceChild($new_node, $old_node);
		return $node;
	
	}

	/**
	 * *******************************************
	 * Méthode cloneNode
	 * ---------------
	 * Cloner l'élément
	 *
	 * ENTRIES
	 * 		BOOLEAN $deep 	Si il faut copier l'arbre au complet
	 * RETURN
	 * 		DOMNode $node 	Node cloné
	 * *******************************************
	 */
	public function cloneNode($deep = true) {

		$node = $this->element->cloneNode($deep);
		return $node;
	
	}

	/**
	 * *******************************************
	 * Méthode importNode
	 * ---------------
	 * Importer un node enfant
	 *
	 * ENTRIES
	 * 		DOMNode $new_node 	Node à importer
	 * RETURN
	 * 		null
	 * *******************************************
	 */
	public function importNode(DOMNode $new_node) {

		$node = $this->element->importNode($new_node, true);
	
	}
    
    /*********************************************
     * Méthode setId
     * ---------------
     * Définir l'attribut Id de l'élément
     * 
     * ENTRIES
     *    INTEGER $id         Id à définir
     * RETURN
     *    null
     *********************************************/
    public function setId($id) {

		return $this->setAttribute("id", $id);
	
	}
    
    /*********************************************
     * Méthode getId
     * ---------------
     * Obtenir l'attribut Id de l'élément
     * 
     * ENTRIES
     *    null
     * RETURN
     *    INTEGER             Id de l'élément
     *********************************************/
    public function getId() {

		if ($this->element->hasAttribute("id")) {
			return $this->getAttribute("id");
		}
	
	}
    
    /*********************************************
     * Méthode getElement
     * ---------------
     * Extraire l'instance de l'élément DOM
     * 
     * ENTRIES
     *    null
     * RETURN
     *    DOMElement          Instance de l'élément DOM
     *********************************************/
    public function getElement() {

		return $this->element;
	
	}
}