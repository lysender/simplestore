<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Security_Index extends Controller_Site
{
	public function action_index()
	{
		$this->view = View::factory('security/index/index');
		$this->template->title = 'Security - System and Users';
	}
}