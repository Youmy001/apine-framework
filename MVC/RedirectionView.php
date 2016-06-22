<?php
namespace Apine\MVC;

/**
 * Redirection View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
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