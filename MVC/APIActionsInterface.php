<?php
/**
 * Reference Controllers
 * This script contains an reference controler for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

/**
 * API Actions Interface
 * Interface for mandatory actions in API controllers
 */
interface APIActionsInterface {

	public function post($params);

	public function get($params);

	public function put($params);

	public function delete($params);
}