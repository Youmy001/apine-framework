<?php
/**
 * APIne Framework Installation Procedure
 * This script runs basic environment setup and launches userside code
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
ini_set('display_errors', -1);
ini_set('include_path', realpath('..'));

$apine_folder = realpath(dirname(__FILE__));
require_once $apine_folder . '/Autoloader.php';
$loader = new Apine\Autoloader();
$loader->add_module('Apine', $apine_folder);
$loader->add_module('Apine\Controllers\System', $apine_folder . '/Controllers');
$loader->register();

use Apine\Core\Request as Request;
use Apine\Exception\GenericException as GenericException;
use Apine\Routing\WebRouter as WebRouter;
use Apine\Controllers\System as Controllers;

$apine = new Apine\Application\Application();
$apine->set_mode(APINE_MODE_DEVELOPMENT);

try {
	if (count(Request::get()) === 0) {
		$controller = new Controllers\InstallController();
		$view = $controller->index();
	} else {
		$args = explode("/", Request::get() ['request']);
		array_shift($args);
		
		if (count($args) > 1) {
			$controller = $args [0];
			array_shift($args);
			$action = $args [0];
			array_shift($args);
		} else {
			$controller = $args [0];
			array_shift($args);
			$action = "index";
		}
		
		// Add post arguments to args array
		if (Request::get_request_type() != "GET") {
			$args = array_merge($args, Request::post());
		}
		
		if (!empty(Request::files())) {
			$args = array_merge($args, array(
					"uploads" => Request::files() 
			));
		}
		
		// print $controller."/".$action;
		$maj_controller = ucfirst($controller) . 'Controller';
		
		if (class_exists('Apine\\Controllers\\System\\' . $maj_controller) && method_exists('Apine\\Controllers\\System\\' . $maj_controller, $action)) {
			$return = 'Apine\\Controllers\\System\\' . $maj_controller;
			$controller = new $return();
			$view = $controller->$action($args);
		} else {
			throw new GenericException('Not Found', 404);
		}
	}
	// $router = new WebRouter();
	// $view = $router->execute('install', 'index');
	// die();
	
	// Draw the output
	if (!is_null($view) && is_a($view, 'Apine\MVC\View')) {
		$view->draw();
	}
} catch (Exception $e) {
	
}