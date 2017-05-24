<?php
/**
 * Pagination utility
 * This script contains a class that draws a pagination navigation
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\MVC;

/**
 * A class that draws a pagination navigation for a list of various
 * items.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\MVC
 * @deprecated
 */
class Pagination
{
    /**
     * Current active page
     *
     * @var integer
     */
    private $current_page;
    
    /**
     * Total number of pages
     *
     * @var integer
     */
    private $number_page;
    
    /**
     * Number of pages to show
     *
     * @var integer
     */
    private $page_display;
    
    /**
     * Base url for links
     *
     * @var string
     */
    private $base;
    
    /**
     * Type of pagination navigation
     *
     * @var integer
     */
    private $type = APINE_PAGINATION_TYPE_PAGINATION;
    
    /**
     * Pagination class' construction
     *
     * @param integer $a_nb_item
     *        Number of items in the item list
     * @param integer $cur_page
     *        Current page in the list
     * @param integer $a_nb_display
     *        Number of items to display per pages
     */
    public function __construct($a_nb_item, $cur_page = 1, $a_nb_display = 10)
    {
        $this->setCurrentPage($cur_page);
        $this->setNumberPage(ceil($a_nb_item / $a_nb_display));
        $this->setPageDisplay($a_nb_display);
    }
    
    /**
     * Set current active page
     *
     * @param integer $a_cur_page
     */
    public function setCurrentPage($a_cur_page)
    {
        $this->current_page = (is_numeric($a_cur_page)) ? (int)$a_cur_page : 0;
    }
    
    /**
     * Fetch current active page
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }
    
    /**
     * Set total number of pages
     *
     * @param integer $a_num_page
     */
    public function setNumberPage($a_num_page)
    {
        $this->number_page = (is_numeric($a_num_page)) ? (int)$a_num_page : 0;
    }
    
    /**
     * Fetch total number of pages
     *
     * @return integer
     */
    public function getNumberPage()
    {
        return $this->number_page;
    }
    
    /**
     * Set the number of pages to display
     *
     * @param integer $a_page_display
     */
    public function setPageDisplay($a_page_display)
    {
        $this->page_display = (is_numeric($a_page_display)) ? $a_page_display : 0;
    }
    
    /**
     * Fetch the number of pages to display
     *
     * @return integer
     */
    public function getPageDisplay()
    {
        return $this->page_display;
    }
    
    /**
     * Set base url for links
     *
     * @param string $a_base
     */
    public function setBaseUrl($a_base)
    {
        $this->base = $a_base;
    }
    
    /**
     * Mark the pagination as a pager
     */
    public function pager()
    {
        $this->type = APINE_PAGINATION_TYPE_PAGER;
    }
    
    /**
     * Mark the pagination as a regular pagination
     */
    public function pagination()
    {
        $this->type = APINE_PAGINATION_TYPE_PAGINATION;
    }
    
    /**
     * Return the first page in the pagination bar
     *
     * @throws \Exception If type is not APINE_PAGINATION_TYPE_PAGINATION
     * @return int
     */
    public function getFirstDisplayPage()
    {
        if ($this->type == APINE_PAGINATION_TYPE_PAGINATION) {
            $split = ceil($this->page_display / 2);
            $begin = $this->current_page - $split;
            $end = $this->current_page + $split;
            
            if ($begin < 1) {
                $begin = 1;
                $end = $this->page_display;
            }
            
            if ($end > $this->number_page) {
                $begin = ($this->number_page - $this->page_display) + 1;
                
                if ($begin < 1) {
                    $begin = 1;
                }
            }
            
            return $begin;
        } else {
            throw new \Exception('Not a pagination bar');
        }
    }
    
    /**
     * Return the number of the last page in the pagination bar
     *
     * @throws \Exception If type is not APINE_PAGINATION_TYPE_PAGINATION
     * @return int
     */
    public function getLastDisplayPage()
    {
        if ($this->type == APINE_PAGINATION_TYPE_PAGINATION) {
            $split = ceil($this->page_display / 2);
            $begin = $this->current_page - $split;
            $end = $this->current_page + $split;
            
            if ($begin < 1) {
                $end = $this->page_display;
            }
            
            if ($end > $this->number_page) {
                $end = (int) $this->number_page;
            }
            
            return $end;
        } else {
            throw new \Exception('Not a pagination bar');
        }
    }
    
    /**
     * Send the pagination bar to output
     */
    public function draw()
    {
        if ($this->type == APINE_PAGINATION_TYPE_PAGINATION) {
            $split = ceil($this->page_display / 2);
            $prev_page = $this->current_page - 1;
            $next_page = $this->current_page + 1;
            $begin = $this->current_page - $split;
            $end = $this->current_page + $split;
            
            if ($begin < 1) {
                $begin = 1;
                $end = $this->page_display;
            }
            
            if ($end > $this->number_page) {
                $end = $this->number_page;
                $begin = ($this->number_page - $this->page_display) + 1;
                
                if ($begin < 1) {
                    $begin = 1;
                }
            }
            
            print '<nav class="text-center">
						<ul class="pagination pagination-lg">';
            
            if ($this->current_page == 1) {
                print '<li id="page_prev" class="disabled"><a href="">&laquo;</a></li>';
            } else {
                print '<li id="page_prev" ><a class="effect" href="' . $this->base . $prev_page;
                print '">&laquo;</a></li>';
            }
            
            for ($i = $begin; $i <= $end; $i++) {
                if ($i == $this->current_page) {
                    print '<li id="page_' . $i . '" class="page active">';
                } else {
                    print '<li id="page_' . $i . '" class="page">';
                }
                
                print "<a class=\"effect\" href=\"" . $this->base . $i;
                print "\">$i</a></li>";
            }
            
            if ($this->current_page == $this->number_page) {
                print '<li id="page_next" class="disabled"><a href="">&raquo;</a></li>';
            } else {
                print '<li id="page_next" ><a class="effect" href="' . $this->base . $next_page;
                print '">&raquo;</a></li>';
            }
            
            print '</ul>
				</nav>';
        } else {
            if ($this->type == APINE_PAGINATION_TYPE_PAGER) {
                $prev_page = $this->current_page - 1;
                $next_page = $this->current_page + 1;
                print '<ul class="pager">';
                
                if ($this->current_page == 1) {
                    print '<li class="previous disabled"><a href=""><span aria-hidden="true">&larr;</span> Older</a></li>';
                } else {
                    print '<li class="previous"><a class=\"effect\" href="' . $this->base . $prev_page;
                    print '"><span aria-hidden="true">&larr;</span> Older</a></li>';
                }
                
                if ($this->current_page == $this->number_page) {
                    print '<li class="next disabled"><a href="">Newer <span aria-hidden="true">&rarr;</span></a></li>';
                } else {
                    print '<li class="next"><a class="effect" href="' . $this->base . $next_page;
                    print '">Newer <span aria-hidden="true">&rarr;</span></a></li>';
                }
                
                print '</ul>';
            }
        }
        
    }
    
}
