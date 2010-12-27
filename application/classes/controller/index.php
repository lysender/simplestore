<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Site
{
	/**
	 * Front page
	 */
	public function action_index()
	{
		$this->template->title = 'Dashboard';
		$this->view = View::factory('index/index');
	}
}
