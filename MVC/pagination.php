<?php
/**
 * #@+
 * Constants
 */
define('PAGINATION_TYPE_PAGINATION', 111);
define('PAGINATION_TYPE_PAGER', 110);

/**
 * A class that draws a pagination navigation for a list of various
 * items.
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package bokaro
 * @subpackage view
 */
class ApinePagination {

	/**
	 * Current active page
	 * @var integer
	 */
	private $cur_page;

	/**
	 * Total number of pages
	 * @var integer
	 */
	private $num_page;

	/**
	 * Number of pages to show
	 * @var integer
	 */
	private $page_display;

	/**
	 * Base url for links
	 * @var string
	 */
	private $base;

	/**
	 * Type of pagination navigation
	 * @var integer
	 */
	private $type = PAGINATION_TYPE_PAGINATION;

	/**
	 * Pagination class' construction
	 * @param integer $a_nb_item
	 *        Number of items in the item list
	 * @param integer $cur_page
	 *        Current page in the list
	 * @param integer $a_nb_display
	 *        Number of items to display per pages
	 */
	public function __construct($a_nb_item, $cur_page = 1, $a_nb_display = 10) {

		$this->set_current_page($cur_page);
		$this->set_number_page(ceil($a_nb_item / $a_nb_display));
		$this->set_page_display($a_nb_display);
	
	}

	/**
	 * Set current active page
	 * @param integer $a_cur_page        
	 */
	public function set_current_page($a_cur_page) {

		(is_numeric($a_cur_page))?$this->cur_page = (int) $a_cur_page:0;
	
	}

	/**
	 * Fetch current active page
	 * @return integer
	 */
	public function get_current_page() {

		return $this->cur_page;
	
	}

	/**
	 * Set total number of pages
	 * @param integer $a_num_page        
	 */
	public function set_number_page($a_num_page) {

		(is_numeric($a_num_page))?$this->num_page = (int) $a_num_page:0;
	
	}

	/**
	 * Fetch total number of pages
	 * @return integer
	 */
	public function get_number_page() {

		return $this->num_page;
	
	}

	/**
	 * Set the number of pages to display
	 * @param integer $a_page_display        
	 */
	public function set_page_display($a_page_display) {

		(is_numeric($a_page_display))?$this->page_display = $a_page_display:0;
	
	}

	/**
	 * Fetch the number of pages to display
	 * @return integer
	 */
	public function get_page_display() {

		return $this->page_display;
	
	}

	/**
	 * Set base url for links
	 * @param string $a_base        
	 */
	public function set_base_url($a_base) {

		$this->base = $a_base;
	
	}

	/**
	 * Mark the pagination as a pager
	 */
	public function pager() {

		$this->type = PAGINATION_TYPE_PAGER;
	
	}

	/**
	 * Mark the pagination as a regular pagination
	 */
	public function pagination() {

		$this->type = PAGINATION_TYPE_PAGINATION;
	
	}

	public function get_first_display_page() {

		if ($this->type == PAGINATION_TYPE_PAGINATION) {
			$split = ceil($this->page_display / 2);
			$prev_page = $this->cur_page - 1;
			$next_page = $this->cur_page + 1;
			$begin = $this->cur_page - $split;
			$end = $this->cur_page + $split;
			
			if ($begin < 1) {
				$begin = 1;
				$end = $this->page_display;
			}
			
			if ($end > $this->num_page) {
				$end = $this->num_page;
				$begin = ($this->num_page - $this->page_display) + 1;
				
				if ($begin < 1) {
					$begin = 1;
				}
			}
			
			return $begin;
		} else {
			
		}
	
	}

	public function get_last_display_page() {

		if ($this->type == PAGINATION_TYPE_PAGINATION) {
			$split = ceil($this->page_display / 2);
			$prev_page = $this->cur_page - 1;
			$next_page = $this->cur_page + 1;
			$begin = $this->cur_page - $split;
			$end = $this->cur_page + $split;
			
			if ($begin < 1) {
				$begin = 1;
				$end = $this->page_display;
			}
			
			if ($end > $this->num_page) {
				$end = $this->num_page;
				$begin = ($this->num_page - $this->page_display) + 1;
				
				if ($begin < 1) {
					$begin = 1;
				}
			}
			
			return $end;
		} else {
			
		}
	
	}

	public function draw() {

		if ($this->type == PAGINATION_TYPE_PAGINATION) {
			$split = ceil($this->page_display / 2);
			$prev_page = $this->cur_page - 1;
			$next_page = $this->cur_page + 1;
			$begin = $this->cur_page - $split;
			$end = $this->cur_page + $split;
			
			if ($begin < 1) {
				$begin = 1;
				$end = $this->page_display;
			}
			
			if ($end > $this->num_page) {
				$end = $this->num_page;
				$begin = ($this->num_page - $this->page_display) + 1;
				
				if ($begin < 1) {
					$begin = 1;
				}
			}
			
			print '<nav class="text-center">
						<ul class="pagination pagination-lg">';
			
			if ($this->cur_page == 1) {
				print '<li id="page_prev" class="disabled"><a href="">&laquo;</a></li>';
			} else {
				print '<li id="page_prev" ><a class="effect" href="' . $this->base . $prev_page;
				print '">&laquo;</a></li>';
			}
			
			for ($i = $begin;$i <= $end;$i++) {
				if ($i == $this->cur_page) {
					print '<li id="page_' . $i . '" class="page active">';
				} else {
					print '<li id="page_' . $i . '" class="page">';
				}
				
				print "<a class=\"effect\" href=\"" . $this->base . $i;
				print "\">$i</a></li>";
			}
			
			if ($this->cur_page == $this->num_page) {
				print '<li id="page_next" class="disabled"><a href="">&raquo;</a></li>';
			} else {
				print '<li id="page_next" ><a class="effect" href="' . $this->base . $next_page;
				print '">&raquo;</a></li>';
			}
			
			print '</ul>
				</nav>';
		} else if($this->type == PAGINATION_TYPE_PAGER) {
			$prev_page = $this->cur_page - 1;
			$next_page = $this->cur_page + 1;
			print '<ul class="pager">';
			
			if ($this->cur_page == 1) {
				print '<li class="previous disabled"><a href="">&larr; ' . PAGER_OLDER . '</a></li>';
			} else {
				print '<li class="previous"><a class=\"effect\" href="' . $this->base . $prev_page;
				print '">&larr; ' . PAGER_OLDER . '</a></li>';
			}
			
			if ($this->cur_page == $this->num_page) {
				print '<li class="next disabled"><a href="">' . PAGER_NEWER . ' &rarr;</a></li>';
			} else {
				print '<li class="next"><a class="effect" href="' . $this->base . $next_page;
				print '">' . PAGER_NEWER . ' &rarr;</a></li>';
			}
			
			print '</ul>';
		}
	
	}

}
