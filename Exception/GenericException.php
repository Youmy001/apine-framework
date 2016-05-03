<?php
/**
 * Custom Exception Handler
 * This script contains a custom exception handler
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Exception;

/**
 * Custom implementation of the exception handler matching
 * more closely a RESTful approach
 * 
 * @author Tommy Teasdale
 */
class GenericException extends \Exception {

	protected $code = 500;

	public function __construct($message = null, $code = 500, \Exception $previous = null){

		parent::__construct($message, $code, $previous);

	}

}