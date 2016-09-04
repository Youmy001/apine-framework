<?php
/**
 * This file contains the session data class
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\Session;

use Apine\Core\Cookie;
use Apine\Core\Encryption;
use Apine\Entity\EntityModel;

/**
 * Manage data for a session
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
final class SessionData extends EntityModel {
	
	/**
	 * Identifier of the session
	 * 
	 * @var string
	 */
	public $session_id;
	
	/**
	 * Data saved in the session
	 * 
	 * @var mixed[]
	 */
	private $data;
	
	/**
	 * Time of the last access to the session
	 * @var string
	 */
	private $last_access;
	
	/**
	 * SessionData class' constructor
	 *
	 * @param string $a_id
	 *        Session Identifier
	 */
	public function __construct ($a_id = null) {
		
		if ($a_id != null) {
			$this->session_id = $a_id;
		} else if (Cookie::get('apine_session') != null) {
			$this->session_id = Cookie::get('apine_session');
		} else {
			$this->session_id = Encryption::token();
		}
		
		$this->_initialize('apine_sessions', $this->session_id);
		
	}
	
	/**
	 * Verify is a session is too old
	 * 
	 * @param integer $delay
     * @return boolean
	 */
	public function is_valid ($delay = 7200) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		return (strtotime($this->last_access) > time() - $delay);
		
	}
	
	/**
	 * Get a session var
	 * 
	 * @param string $a_name
     * @return mixed
	 */
	public function get_var ($a_name) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (isset($this->data[$a_name])) {
			return $this->data[$a_name];
		}
		
	}
	
	/**
	 * Set a session var
	 * 
	 * @param string $a_name
	 * @param mixed $a_value
	 */
	public function set_var ($a_name, $a_value) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->data[$a_name] = $a_value;

	}
	
	/**
	 * Unset a session var
	 * 
	 * @param string $a_name
	 */
	public function remove_var ($a_name) {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		unset($this->data[$a_name]);
		
	}
	
	/**
	 * Reset session vars
	 */
	public function reset () {
		
		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->data = array();
		
	}
	
	/**
	 *
	 * @see EntityInterface::load()
	 */
	public function load () {
		
		$this->data = json_decode($this->_get_field('data'), true);
		$this->last_access = $this->_get_field('last_access');
		$this->loaded = 1;
		
	}
	
	/**
	 *
	 * @see EntityInterface::save()
	 */
	public function save () {
		
		$this->_set_field('id', $this->session_id);
		$this->_set_field('data', json_encode($this->data));
		$this->_set_field('last_access', date('Y-m-d H:i:s', time()));
		
		$this->_save();
		
	}
	
	/**
	 *
	 * @see EntityInterface::delete()
	 */
	public function delete () {
		
		$this->_destroy();
		
	}
	
	/**
	 * SessionData class' destructor
	 */
	public function __destruct() {
		
		$this->save();
		
	}
	
}