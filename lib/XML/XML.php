<?php
namespace Apine\XML;

use \DOMDocument;

/*
/ Projet Marie-Delice 
/ Classe modèle de XML pour l'application Web
/ License : 
/ Auteurs: Marc-André Douville Auger, Véronique Blais, Tommy Teasdale
*/
/*************************************************
 * Classe XML
 * ---------------
 * Encapsulation de la classe DOM_Document
 *
 * Cette classe permet de créer, charger, générer
 * et sauvegarder des documents XML. 
 *************************************************/
class XML {
	
    private $xml_file;    // Chemin du fichier
    
    protected $DOM_document;    // Instance du DOM
    
    private $html;		// Est-ce un HTML?
    
    /*********************************************
     * Méthode __construct
     * ---------------
     * Constructeur de la classe XML
     * 
     * ENTRIES
     *    null
     * RETURN
     *    null
     *********************************************/
    public function __construct() {
    	
    	$this->DOM_document=new DOMDocument("1.0","UTF-8");
    	$this->html=false;
    	
    }
    
    /*********************************************
     * Méthode load_from_string
     * ---------------
     * Ouvrir un Document XML avec une chaîne de caractère
     * 
     * ENTRIES
     *    STRING $a_xml_string Document XML sous forme
     *                         de chaîne de caractères
     * RETURN
     *    null
     *********************************************/
    public function load_from_string($a_xml_string,$options) {
    	
    	$this->DOM_document->loadXML($a_xml_string,$options);
    
    }
    
    /*********************************************
     * Méthode load_from_html
     * ---------------
     * Ouvrir un Document HTML avec une chaîne de caractère
     *
     * ENTRIES
     *    STRING $a_html_string Document HTML sous forme
     *                         de chaîne de caractères
     * RETURN
     *    null
     *********************************************/
    public function load_from_html($a_html_string) {
    	
    	$this->DOM_document=new DOMDocument();
    	libxml_use_internal_errors(true);
    	$this->DOM_document->loadHTML($a_html_string,LIBXML_NOXMLDECL+LIBXML_HTML_NODEFDTD+LIBXML_HTML_NOIMPLIED+LIBXML_NOWARNING);
    	libxml_clear_errors();
    	$this->html=true;
    	
    }
    
    /*********************************************
     * Méthode load_from_file
     * ---------------
     * Ouvrir un Document XML avec un ficheir
     * 
     * ENTRIES
     *    STRING $a_xml_file  Chemin vers le fichier XML
     * RETURN
     *    null
     *********************************************/
    public function load_from_file($a_xml_file) {
    	
    	$this->set_xml_file($a_xml_file);
    	$this->DOM_document->load($a_xml_file);
    	
    }
    
    /*********************************************
     * Méthode set_xml_file
     * ---------------
     * Définir le chemin d'enregistrement du fichier
     * 
     * ENTRIES
     *    STRING $a_xml_file  Chemin vers le fichier XML
     * RETURN
     *    null
     *********************************************/
    public function set_xml_file($a_xml_file) {
    	
    	$this->xml_file=$a_xml_file;
    	
    }
    
    /*********************************************
     * Méthode get_xml_file
     * ---------------
     * Obtenir le chemin d'enregistrement du fichier
     * 
     * ENTRIES
     *    null
     * RETURN
     *    STRING              Chemin vers le fichier XML
     *********************************************/
    public function get_xml_file() {
    	
    	return $this->xml_file;
    	
    }
    
    private function is_html() {
    	
    	return $this->html;
    	
    }
    
    /*********************************************
     * Méthode __toString
     * ---------------
     * Obtenir le contenu du document en chaîne de carctère
     * 
     * ENTRIES
     *    null
     * RETURN
     *    STRING              Contenu du document
     *********************************************/
    public function __toString() {
    	
    	if (!$this->is_html()) {
    		return $this->DOM_document->saveXML();
    	} else {
    		$this->DOM_document->encoding = 'UTF-8';
    		return $this->DOM_document->saveHTML();
    	}
    	
    }
    
    /*********************************************
     * Méthode Save
     * ---------------
     * Sauvegarder le document XML dans un fichier
     * 
     * ENTRIES
     *    null
     * RETURN
     *    INTEGER             1 si l'enregistrement s'est terminé avec succès
     *                        0 si une erreur s'est produite
     *********************************************/
    public function Save() {
    	
    	// Retourne 1 au succès de la sauvegarde dans un fichier
    	// Retourne 0 lorsque la sauvegarde a échouée ou lorsqu'aucun nom de fichier n'est spécifié
    	try {
    		if ($this->get_xml_file()!="") {
    			$save_state=$this->DOM_document->save($this->get_xml_file());
    			if ($save_state==false) {
    				throw new Exception("Une erreur s'est produite lors de l'enregistrement");
    			}
    		} else {
    			throw new Exception("L'enregistrement est impossible. Aucun chemin n'a été spécifié.");
    		}
    		
    		return 1;
    	} catch(Exception $e) {
    		throw new Exception($e->getMessage()." - ".$e->getTraceAsString());
    		return 0; 
    	}
    	
    }
    
}
