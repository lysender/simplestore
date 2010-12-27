<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Basic pagination
 * 
 * @author lysender
 *
 */
class Dc_Paginate
{
	public $max;
	
	public $per_page = 20;
	
	public $per_nav = 10;
	
	public $page_prefix = '?page=';
	
	public $prev_link = 'Previous';
	
	public $next_link = 'Next';
	
	public $show_total = TRUE;
	
	/**
	 * Renders a pagination links. Only renders the nearest {$page_page}
	 * links to the current page
	 *
	 * @param int $max			Maximum items
	 * @param int $per_page		Items per page
	 * @param int $page			Current page
	 * @return string
	 */
	public function render($base_url, $page_prefix, $max, $per_page, $page = NULL)
	{
		if ( ! $max || ($max <= $per_page))
		{
			return NULL;
		}
		
		// get total pages
		$s = '';
		$page = (int) $page;
		$total_pages = ceil($max / $per_page);
		
		// identify the current page
		if ($page < 1)
		{
			$page = 1;
		}
		else if ($page > $total_pages)
		{
			$page = $total_pages;
		}
		
		// Identify the start and end page links to display
		$start = NULL;
		if ($page <= $this->per_nav)
		{
			$start = 1;
		}
		else
		{
			if ($page % $this->per_nav == 0)
			{
				$start = ((($page / $this->per_nav) - 1) * $this->per_nav) + 1;
			}
			else
			{
				$start = (floor($page / $this->per_nav) * $this->per_nav) + 1;
			}
		}
		
		$end = $start + ($this->per_nav - 1);
		if ($end > $total_pages)
		{
			$end = $total_pages;
		}
		
		$s .= '<p class="paginator">';
		
		// Show the first page when it is not visible from the nav
		if ($start > $this->per_nav)
		{
			$first = '<a class="paginate-first-page" href="'.URL::site($base_url).'">&nbsp;1&nbsp;</a>';
			$s .= $first.' ... ';
		}
		
		// Add a link to previous page only when we are not on the first page
		if ($page > 1)
		{
			$prev = '<a class="paginate-previous-page" href="'.URL::site(($page_prefix.($page - 1))).'">'.$this->prev_link.'</a>';
			$s .= $prev;
		}
		
		// add up to {self::$per_nav} pages
		for ($x = $start; $x <= $end; $x++)
		{
			// apply current page if possible
			$class = '';
			if ($x == $page)
			{
				// no link for current
				$s .= '<span class="paginate-selected-page">' . $x . '</span>';
			}
			else
			{
				$url = NULL;
				if ($x == 1)
				{
					$url = URL::site($base_url);
				}
				else
				{
					$url = URL::site($page_prefix.$x);
				}
				
				$s .= '<a class="paginate-page" href="'.$url.'">'.$x.'</a>';
			}
		}
		
		// Add a link to next page only when we are not on the last page
		if ($page < $total_pages)
		{
			$next = '<a class="paginate-next-page" href="'.URL::site(($page_prefix.($page + 1))).'">'.$this->next_link.'</a>';
			$s .= $next;
		}
		
		// Add link to last oage only when it is not visible
		if ($page < $total_pages && $end < $total_pages)
		{
			$last = '<a class="paginate-last-page" href="'.URL::site(($page_prefix.$total_pages)).'">'.$total_pages.'</a>';
			$s .= ' ... ' . $last;
		}
		
		if ($this->show_total)
		{
			$s .= ' <span class="paginate-total">Total records: '.$max.'</span>';
		}
		
		// End tag
		$s .= '</p>';
		
		return $s;
	}
}