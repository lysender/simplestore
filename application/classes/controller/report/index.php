<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Report_Index extends Controller_Site
{
	public function action_index()
	{
		$this->view = View::factory('report/index/index');
		$this->template->title = 'Reports - Store Performance';
	}
}