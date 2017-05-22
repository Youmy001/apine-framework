<?php
/**
 * Interface for routers
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Routing;

/**
 * Interface RouterInterface
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Routing
 */
interface RouterInterface {

	/**
	 * Route the request to the best matching controller and action
	 *
	 * @param string $request
	 * @return Route
	 */
	public function route ($request);

	/**
	 * Execute an action
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 */
	public function execute ($controller, $action, $args);

}