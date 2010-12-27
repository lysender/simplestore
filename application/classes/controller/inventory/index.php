<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Index extends Controller_Site
{
	public function action_index()
	{
		$this->view = View::factory('inventory/index/index');
		$this->template->title = 'Inventory - Items and Pricing';
	}
}