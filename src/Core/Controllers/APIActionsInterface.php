<?php
/**
 * Interface for API Actions
 *
 * @license MIT
 * @copyright 2015-18 Tommy Teasdale
 */

namespace Apine\Core\Controllers;

use Apine\Core\Views\View;

/**
 * API Actions Interface
 * Interface for mandatory actions in API controllers
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core\Controllers
 */
interface APIActionsInterface
{
    /**
     * @param $params
     *
     * @return View
     */
    public function post($params);
    
    /**
     * @param $params
     *
     * @return View
     */
    public function get($params);
    
    /**
     * @param $params
     *
     * @return View
     */
    public function put($params);
    
    /**
     * @param $params
     *
     * @return View
     */
    public function delete($params);
}