<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 17/05/06
 * Time: 23:21
 */

namespace Apine\Event;


final class Event {
	
	private $name;
	
	private $target;
	
	private $parameters = [];
	
	private $propagationStopped = false;
	
	/**
	 * Get event name
	 *
	 * @return string
	 */
	public function getName () {
		return $this->name;
	}
	
	/**
	 * Get target/context from which event was triggered
	 *
	 * @return null|string|object
	 */
	public function getTarget () {
		return $this->target;
	}
	
	/**
	 * Get parameters passed to the event
	 *
	 * @return array
	 */
	public function getParams () {
		return $this->parameters;
	}
	
	/**
	 * Get a single parameter by name
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function getParam ($name) {
		return $this->parameters[$name];
	}
	
	/**
	 * Set the event name
	 *
	 * @param  string $name
	 * @return void
	 */
	public function setName ($name) {
		$this->name = (string) $name;
	}
	
	/**
	 * Set the event target
	 *
	 * @param  null|string|object $target
	 * @return void
	 */
	public function setTarget ($target) {
		$this->target = $target;
	}
	
	/**
	 * Set event parameters
	 *
	 * @param  array $params
	 * @return void
	 */
	public function setParams (array $params) {
		$this->parameters = $params;
	}
	
	/**
	 * Indicate whether or not to stop propagating this event
	 *
	 * @param  bool $flag
	 */
	public function stopPropagation ($flag) {
		$this->propagationStopped = (bool) $flag;
	}
	
	/**
	 * Has this event indicated event propagation should stop?
	 *
	 * @return bool
	 */
	public function isPropagationStopped () {
		return (bool) $this->propagationStopped;
	}
}