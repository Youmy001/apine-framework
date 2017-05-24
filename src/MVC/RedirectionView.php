<?php
/**
 * Redirection View Abstraction
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */

namespace Apine\MVC;

/**
 * Redirection View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 */
final class RedirectionView extends View
{
    /**
     * Send View to output
     */
    public function draw()
    {
        $this->applyHeaders();
        
        print $this->content();
    }
    
    /**
     * Return the content of the view
     *
     * @return string
     */
    public function content()
    {
        return '';
    }
    
}