<?php
/**
 * User Groups
 * This script contains a class to manage user groups
 *  
 * @license MIT
 * @copyright 2015 François Allard
 */
namespace Apine\User;

use Apine;
use Apine\Entity\Overload\EntityModel;

/**
 * Implementation of the database representation of users groups
 * 
 * @author François Allard <allard.f@kitaiweb.ca>
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\User
 *
 * @method string get_name()
 * @method set_name(string $string)
 */
class UserGroup extends EntityModel {

	/**
	 * Group's name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Group class' constructor
	 *
	 * @param integer $a_id
	 *        Group identifier
	 */
	public function __construct ($a_id = null) {

		parent::initialize('apine_user_groups', $a_id);

	}

}