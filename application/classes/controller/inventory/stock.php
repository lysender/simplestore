<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Stock pages
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Stock extends Controller_Site
{
	/** 
	 * Stock list page
	 * 
	 */
	public function action_index()
	{
		$this->template->title = 'Stock';
		$this->view = View::factory('inventory/stock/index');
		
		$stock = Sprig::factory('stock');
		
		// Reuse route id as page
		$page = Arr::get($_GET, 'page');
		
		// Reuse route id as category
		$category_id = $this->request->param('id');
		
		$this->view->stocks = $stock->get_paged($page);
	}
	
	/** 
	 * Add to stock page
	 * 
	 */
	public function action_add()
	{
		$this->template->title = 'Stock - Add';
		$this->view = View::factory('inventory/stock/add');
	}
}