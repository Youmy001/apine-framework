<?php
/**
 * Home Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use Apine\MVC\Controller;
use Apine\MVC\TwigView;

/**
 * Class HomeController
 *
 * Placeholder controller for a home page
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class HomeController extends Controller {

    /**
     * Default Action
     *
     * @return HTMLView
     */
	public function index () {

	    $view = new TwigView();

		$view->set_title('Home');
		$view->set_view('home');
		
		return $view;
		
	}
}