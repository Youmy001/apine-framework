<?php
/**
 * Password Restoration Token
 * This script contains the model representation of password restoration tokens
 *
 * @license MIT
 * @copyright 2015-2016 Tommy Teasdale
 */
namespace Apine\User;

use Apine;
use Apine\Entity\Overload\EntityModel;

/**
 * Implementation of the database representation of password restoration tokens
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 *
 * @method string get_token() Fetch the value of the token string
 * @method string get_creation_date() Fetch the date of the creation of the token
 */
class PasswordToken extends EntityModel {
	
	/**
	 * Token user
	 *
	 * @var User
	 */
	protected $user;
	
	/**
	 * Token string
	 *
	 * @var string
	 */
	protected $token;
	
	/**
	 * Token creation date
	 *
	 * @var string
	 */
	protected $creation_date;
	
	/**
	 * PasswordToken class' constructor
	 *
	 * @param integer $a_id
	 *        Token identifier
	 */
	public function __construct($a_id = null) {
		
		$this->initialize('apine_password_tokens', $a_id);
		
	}
	
	/**
	 * Set token string
	 * 
	 * @param string $a_token
	 */
	public function set_token ($a_token) {
	
		if (strlen($a_token) == 64) {
			parent::set_token($a_token);
		}
	}
	
	/**
	 * Set token's creation date
	 * 
	 * @param string $a_timestamp
	 *        Token's creation date
	 * @throws \Exception If the the UNIX Timestamp is invalid
	 */
	public function set_creation_date ($a_timestamp) {

		if (is_string($a_timestamp) && strtotime($a_timestamp)) {
			$creation_date = date('Y-m-d H:i:s', strtotime($a_timestamp));
		} else if (is_long($a_timestamp) && date('u', $a_timestamp)) {
			$creation_date = date('Y-m-d H:i:s', $a_timestamp);
		} else {
			throw new \Exception('Invalid UNIX Timestamp');
		}

		parent::set_creation_date($creation_date);
	
	}
	
	/**
	 * Fetch the token user
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
	 * Set the token user
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
	 * @see EntityInterface::save()
	 */
	public function save () {
		
		if ($this->get_token() === null || $this->get_user() === null) {
			throw new Apine\Exception\GenericException('Missing values', 500);
		}
	
		parent::save();
	
	}
	
}