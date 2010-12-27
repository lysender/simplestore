<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Items extends Controller_Site
{
	public function action_index()
	{
		$this->view = View::factory('inventory/items/index');
		$this->template->title = 'Item Masterlist';
	}
}