<?php
/**
 * This file contains the interface for sesssion managers
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
namespace Apine\Session;

/**
 * Interface SessionInterface
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Session
 */
interface SessionInterface {

	/**
	 * @return boolean
	 */
	public function is_logged_in ();

	/**
	 * @return \Apine\User\User
	 */
	public function get_user ();

	/**
	 * @return integer
	 */
	public function get_user_id ();

	/**
	 * @return string
	 */
	public function get_session_identifier();

	/**
	 * @return integer
	 */
	public function get_session_type ();

	/**
	 * @param integer $a_type
	 */
	public function set_session_type ($a_type);

	/**
	 * @return boolean
	 */
	public function is_session_admin();

	/**
	 * @return boolean
	 */
	public function is_session_normal();

	/**
	 * @return boolean
	 */
	public function is_session_guest();

	/**
	 * @param string $a_username
	 * @param string $a_password
	 * @return boolean
	 */
	public function login ($a_username, $a_password);

	/**
	 * @return boolean
	 */
	public function logout ();

}