<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Item categories controller
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Category extends Controller_Site
{
	/** 
	 * @var Model_Category
	 */
	protected $_category;
	
	/** 
	 * Make sure id is valid on edit/delete
	 * 
	 * (non-PHPdoc)
	 * @see application/classes/controller/Controller_Site::before()
	 */
	public function before()
	{
		parent::before();
		
		if (in_array($this->request->action, array('edit', 'delete')))
		{
			if ($id = $this->request->param('id'))
			{
				$this->_category = Sprig::factory('category', 
					array('id' => $id)
				)->load();
			}
			
			if ($this->_category === NULL || ! $this->_category->loaded())
			{
				$this->session->set('error_message', 'Invalid parameter');
				$this->request->redirect('/inventory/category');
			}
		}
	}
	
	/** 
	 * Category list
	 * 
	 */
	public function action_index()
	{
		$this->view = View::factory('inventory/category/index');
		$this->template->title = 'Item Categories';
		
		$this->template->scripts[] = 'media/js/crud.js';
		$this->template->scripts[] = 'media/js/jquery.tablesorter.min.js';
		$this->template->head_readyscripts .= '
			$(".reg-list").tablesorter({
				widgets: ["zebra"],
				headers: {
					0: {sorter: false},
					3: {sorter: false}
				}
			});
		';
		
		$category = Sprig::factory('category');
		
		$this->view->categories = $category->get_all();
	}
	
	/** 
	 * Add page
	 * 
	 */
	public function action_add()
	{
		$this->view = View::factory('inventory/category/add');
		$this->template->title = 'Item Categories - Add';
		
		$category = Sprig::factory('category');
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
	 * Edit page
	 * 
	 */
	public function action_edit()
	{
		$this->view = View::factory('inventory/category/edit');
		$this->template->title = 'Item Categories - Edit';
		
		$csrf_check = TRUE;
		
		if (Request::$method == 'POST')
		{
			$this->_category->values($_POST);
			
			if ($csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				try
				{
					$this->_category->update();
					
					$this->session->set('success_message', 'Category has been updated');
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
		
		$this->view->category = $this->_category;
	}
	
	/** 
	 * Delete page
	 * 
	 */
	public function action_delete()
	{
		// Only allow delete via POST methos
		$delete_params = Arr::extract($_POST, array('referer', 'target'));
		
		if ($delete_params['referer'] === NULL || $delete_params['target'] === NULL)
		{
			$this->view = View::factory('site/cruddelete');
			
			$this->template->title = 'Delete category';
			
			$this->view->delete_subject = 'school';
			$this->view->delete_referer = URL::site('/inventory/category');
			$this->view->delete_target = URL::site('/inventory/category/delete/'.$this->_category->id);
			
			$this->view->delete_record_key = $this->_category->id;
			$this->view->delete_record_detail = $this->_category->name;
		}
		else 
		{
			// Disable template
			$this->auto_render = FALSE;
			$csrf_check = TRUE;
			
			if (Arr::get($_POST, 'yes') && $csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				// Delete
				if ($this->_category->delete())
				{
					// Success
					$this->session->set('success_message', 'Category has been deleted');
				}
				else
				{
					$this->session->set('error_message', 'There was a problem while deleting the category');
				}
			}
			
			if ( ! $csrf_check)
			{
				$this->session->set('error_message', 'Session time out, try again');
			}
			
			$this->request->redirect('/inventory/category');
		}
	}
}