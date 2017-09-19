<?php
/**
 * Redirection View Abstraction
 *
 * @license MIT
 * @copyright 2016-2017 Tommy Teasdale
 */
namespace Apine\MVC;

/**
 * Redirection View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
final class RedirectionView extends View {

	/**
	 * Send View to output
	 */
	public function draw() {

		$this->apply_headers();
		
		print $this->content();

	}

	/**
	 * Return the content of the view
	 *
	 * @return string
	 */
	public function content () {
		
		return '';

	}

}