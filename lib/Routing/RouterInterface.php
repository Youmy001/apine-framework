<?php
namespace Apine\Routing;

interface RouterInterface {

	/**
	 * Route the request to the best matching controller and action
	 *
	 * @param string $request
	 * @return ApineRoute
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