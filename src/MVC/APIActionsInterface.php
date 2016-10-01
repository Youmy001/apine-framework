<?php
/**
 * Interface for API Actions
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\MVC;

/**
 * API Actions Interface
 * Interface for mandatory actions in API controllers
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
interface APIActionsInterface {

    /**
     * @param $params
     * @return View
     */
	public function post($params);

    /**
     * @param $params
     * @return View
     */
	public function get($params);

    /**
     * @param $params
     * @return View
     */
	public function put($params);

    /**
     * @param $params
     * @return View
     */
	public function delete($params);
}