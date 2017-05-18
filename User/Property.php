<?php
/**
 * This file contains the user property class
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\User;

use Apine\Entity\Overload\EntityModel;

/**
 * Implementation of the database representation of user properties
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 *
 * @method string get_name() Get the name of the property
 * @method set_name(string $a_name) Set the name of the property
 */
final class Property extends EntityModel {

    /**
     * @var User
     */
	protected $user;

    /**
     * @var string
     */
	protected $name;

    /**
     * @var mixed
     */
	protected $value;

    /**
     * Property constructor.
     *
     * @param integer $a_id
     */
	public function __construct($a_id = null) {
		
		$this->initialize('apine_user_properties', $a_id);
		
	}

	/**
	 * Get the owner of the property
	 *
	 * @return User
	 */
	public function get_user () {

		if (is_null($this->user)) {
			$this->user = Factory\UserFactory::create_by_id($this->get('user_id'));
		}

		return $this->user;

	}
	
	/**
	 * Set the owner of the property
	 *
	 * @param User|integer $a_user
	 * @throws \Exception If the input value is invalid
	 */
	public function set_user ($a_user) {
	
		if (is_numeric($a_user) && Factory\UserFactory::is_id_exist($a_user)) {
			$this->user = Factory\UserFactory::create_by_id($a_user);
		} else if (is_a($a_user, 'Apine\User\User')) {
			$this->user = $a_user;
		} else {
			throw new \Exception('Invalid User');
		}
	
		$this->set('user_id', $this->user->get_id());
	
	}

	/**
	 * Fetch the value of the property
	 *
	 * @return mixed
	 */
	public function get_value () {

		if (is_null($this->value)) {
			$value = $this->get('value');
			
			if (@unserialize($value) !== false) {
				$this->value = @unserialize($value);
			} else {
				if ($value === serialize(false)) {
					$this->value = false;
				} else {
					$this->value = $value;
				}
			}
		}

		return $this->value;


	}

    /**
     * Set the value of the property
     *
     * @param mixed $a_value
     */
	public function set_value ($a_value) {
		
		if ( null !== $value = serialize($a_value)) {
			$this->value = $a_value;

			if (!is_null($a_value)) {
				$this->set('value', serialize($a_value));
			} else {
				$this->set('value', null);
			}
		}
		
	}
	
	public function serialize ($data = false) {
		
		/*$array = array();
		$array['value'] = $this->get_value();
		$array['name'] = $this->get('name');
		return $array;*/
		if ($data) {
			return $this->get_value();
		} else {
			return parent::serialize();
		}
		
	}
	
}