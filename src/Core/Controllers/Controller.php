<?php
/**
 * Reference Controllers
 * This script contains an reference controler for MVC pattern implementation
 *
 * @license MIT
 * @copyright 2015-18 Tommy Teasdale
 */

namespace Apine\Core\Controllers;

use Apine\Core\Request as Request;
use Psr\Container\ContainerInterface;

/**
 * Basic Controller
 * Describes basics for user controllers
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Controllers
 */
abstract class Controller
{

}

class_alias(Controller::class, 'Apine\Controllers');