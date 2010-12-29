<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Item extends Controller_Site
{
	/** 
	 * Item list page
	 * 
	 */
	public function action_index()
	{
		$this->view = View::factory('inventory/item/index');
		$this->template->title = 'Item Masterlist';
		
		$item = Sprig::factory('item');
		
		$this->template->items = $item->get_all();
	}
	
	/** 
	 * Add item page
	 * 
	 */
	public function action_add()
	{
		$this->view = View::factory('inventory/item/add');
		$this->template->title = 'Item - Add';
		
		$category = Sprig::factory('item');
		$csrf_check = TRUE;
		
		if (Request::$method == 'POST')
		{
			$category->values($_POST);
			
			if ($csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				try
				{
					$category->create();
					
					$this->session->set('success_message', 'A new category has been added');
					$this->request->redirect('/inventory/category');
				}
				catch (Validate_Exception $e)
				{
					$this->_page_error($e->array->errors('category'));
				}
				catch (Exception $e)
				{
					$this->_page_error($e->getMessage(), 'name');
				}
			}
			else 
			{
				$this->_page_error('Session timeout, try again');
			}
		}
		else
		{			
			$this->_page_setfocus('name');
		}
		
		$this->view->category = $category;
	}
	
	/** 
	 * Edit item page
	 * 
	 */
	public function action_edit()
	{
		
	}
	
	/** 
	 * Item delete
	 * 
	 */
	public function action_delete()
	{
		
	}
}