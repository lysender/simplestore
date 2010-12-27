<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Sales_Index extends Controller_Site
{
	public function action_index()
	{
		$this->view = View::factory('sales/index/index');
		$this->template->title = 'Sales - Item Movement';
	}
}