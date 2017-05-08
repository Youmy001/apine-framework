<?php
/**
 * Home Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\MVC as MVC;
use Apine\Core\Request;
use Apine\User\User;

/**
 * Class HomeController
 *
 * Placeholder controller for a home page
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class HomeController extends MVC\Controller {

    /**
     * Default Action
     *
     * @return MVC\HTMLView
     */
	public function index () {
		
		return new MVC\HTMLView('view','Page Title', ['name'=>'value']);
		
	}
	
	public function inputTest ($input) {
		
	}
	
	public function inputTestTwo ($first, $second) {
		
	}
	
	public function inputTestMultiple ($first, $second, $third) {
		
	}
	
	public function inputTestObject (User $user) {
		
	}
	
	public function inputTestObjectOther (User $user, $other) {
		
	}
	
	public function inputTestObjectOtherMultiple (Request $first, User $user, $other) {
		
	}
}